<?php

namespace App\Http\Controllers;

use App\Jobs\SendBookingNotification;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Services\AuditLogger;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function dashboard(): View
    {
        $stats = [
            'pending' => Booking::where('status', 'PENDING')->count(),
            'confirmed' => Booking::where('status', 'CONFIRMED')->count(),
            'today' => Booking::whereDate('appointment_date', today())->count(),
            'upcoming' => Booking::whereDate('appointment_date', '>=', today())->whereIn('status', ['CONFIRMED', 'IN_PROGRESS'])->count(),
            'completed' => Booking::where('status', 'COMPLETED')->count(),
            'cancelled' => Booking::whereIn('status', ['CANCELLED', 'NO_SHOW'])->count(),
            'enquiries' => ContactMessage::where('status', 'NEW')->count(),
            'services' => Service::where('is_active', true)->count(),
            'packages' => Package::where('is_active', true)->count(),
        ];
        $bookings = Booking::with(['service', 'package'])->latest()->limit(8)->get();
        return view('admin.dashboard', compact('stats', 'bookings'));
    }

    public function bookings(): View
    {
        $query = Booking::with(['service', 'package'])->latest();
        if ($search = request('q')) {
            $query->where(function ($builder) use ($search) {
                $builder->where('full_name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($status = request('status')) $query->where('status', $status);
        if ($serviceId = request('service_id')) $query->where('service_id', $serviceId);
        if ($date = request('date')) $query->where(function ($builder) use ($date) { $builder->whereDate('preferred_date', $date)->orWhereDate('appointment_date', $date); });

        return view('admin.bookings', [
            'bookings' => $query->paginate(20)->withQueryString(),
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
            'statuses' => Booking::STATUSES,
        ]);
    }

    public function updateBooking(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', Booking::STATUSES)],
            'appointment_date' => ['nullable', 'date'],
            'appointment_time' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:500'],
            'internal_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $statusBeforeUpdate = $booking->status;
        $appointmentBeforeUpdate = optional($booking->appointment_date)->toDateString().' '.$booking->appointment_time;
        try {
            DB::transaction(function () use ($request, $booking, $data) {
                $locked = Booking::query()->lockForUpdate()->findOrFail($booking->id);
                if (! $locked->canTransitionTo($data['status'])) {
                    abort(422, "Cannot move {$locked->status} to {$data['status']}.");
                }
                $currentDate = optional($locked->appointment_date)->toDateString();
                $nextDate = array_key_exists('appointment_date', $data) ? $data['appointment_date'] : $currentDate;
                $nextTime = array_key_exists('appointment_time', $data) ? $data['appointment_time'] : $locked->appointment_time;
                $slotChanged = $locked->status === 'CONFIRMED' && ($data['status'] !== 'CONFIRMED' || $currentDate !== $nextDate || $locked->appointment_time !== $nextTime);
                if ($slotChanged) DB::table('booking_slots')->where('booking_id', $locked->id)->delete();
                if ($data['status'] === 'CONFIRMED' && $nextDate && $nextTime) {
                    $legacyConflict = Booking::query()->where('id', '<>', $locked->id)->where('status', 'CONFIRMED')->whereDate('appointment_date', $nextDate)->where('appointment_time', $nextTime)->exists();
                    if ($legacyConflict) abort(422, 'That appointment slot is already assigned to another confirmed booking.');
                    DB::table('booking_slots')->insertOrIgnore(['appointment_date' => $nextDate, 'appointment_time' => $nextTime, 'booking_id' => $locked->id, 'created_at' => now(), 'updated_at' => now()]);
                    $slot = DB::table('booking_slots')->where(['appointment_date' => $nextDate, 'appointment_time' => $nextTime])->lockForUpdate()->first();
                    if (! $slot || (int) $slot->booking_id !== (int) $locked->id) abort(422, 'That appointment slot is already assigned to another confirmed booking.');
                }
                $previous = $locked->status;
                $locked->update([
                    'status' => $data['status'],
                    'appointment_date' => $data['appointment_date'] ?? $locked->appointment_date,
                    'appointment_time' => $data['appointment_time'] ?? $locked->appointment_time,
                    'internal_notes' => $data['internal_notes'] ?? $locked->internal_notes,
                    'confirmed_at' => $data['status'] === 'CONFIRMED' ? now() : $locked->confirmed_at,
                    'contacted_at' => in_array($data['status'], ['CONTACTED', 'CONFIRMED'], true) ? ($locked->contacted_at ?: now()) : $locked->contacted_at,
                ]);
                if ($previous !== $data['status'] || $data['note']) {
                    BookingStatusHistory::create([
                        'booking_id' => $locked->id,
                        'from_status' => $previous,
                        'to_status' => $data['status'],
                        'changed_by' => $request->user()->id,
                        'note' => $data['note'] ?? null,
                    ]);
                }
                $this->auditLogger->record($request, 'booking.updated', $locked, ['from' => $previous, 'to' => $data['status']]);
            });
        } catch (QueryException $exception) {
            return back()->withErrors(['status' => 'The booking could not be updated safely.']);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return back()->withErrors(['status' => $exception->getMessage()]);
        }

        $appointmentAfterUpdate = ($data['appointment_date'] ?? optional($booking->appointment_date)->toDateString()).' '.($data['appointment_time'] ?? $booking->appointment_time);
        $event = $data['status'] === 'CANCELLED' ? 'cancelled' : ($data['status'] === 'CONFIRMED' && $statusBeforeUpdate !== 'CONFIRMED' ? 'confirmed' : ($appointmentBeforeUpdate !== $appointmentAfterUpdate ? 'rescheduled' : strtolower($data['status'])));
        SendBookingNotification::dispatchAfterResponse($booking->id, $event);
        return back()->with('success', "Booking {$booking->reference} updated.");
    }

    public function showBooking(Booking $booking): View
    {
        $booking->load(['service', 'package', 'customer', 'vehicle', 'history.user']);
        return view('admin.booking-show', compact('booking'));
    }

    public function content(): View
    {
        return view('admin.content', [
            'services' => Service::orderBy('sort_order')->get(),
            'packages' => Package::orderBy('sort_order')->get(),
            'gallery' => GalleryItem::orderBy('sort_order')->get(),
            'reviews' => Review::latest()->get(),
        ]);
    }

    public function toggleService(Service $service): RedirectResponse
    {
        $service->update(['is_active' => ! $service->is_active]);
        return back()->with('success', "Service {$service->name} visibility updated.");
    }

    public function togglePackage(Package $package): RedirectResponse
    {
        $package->update(['is_active' => ! $package->is_active]);
        return back()->with('success', "Package {$package->name} visibility updated.");
    }

    public function toggleGallery(GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->update(['is_published' => ! $galleryItem->is_published]);
        return back()->with('success', "Gallery item visibility updated.");
    }

    public function toggleReview(Review $review): RedirectResponse
    {
        $review->update(['is_published' => ! $review->is_published]);
        return back()->with('success', "Review visibility updated.");
    }
}
