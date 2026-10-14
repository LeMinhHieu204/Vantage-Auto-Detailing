<?php

namespace App\Http\Controllers;

use App\Jobs\SendBookingNotification;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Service;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function store(StoreBookingRequest $request): RedirectResponse
    {
        $key = 'booking-request:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput()->withErrors(['email' => 'Too many requests. Please try again in a few minutes.']);
        }
        RateLimiter::hit($key, 600);

        $data = $request->validated();
        unset($data['privacy_consent'], $data['website']);

        $booking = DB::transaction(function () use ($data) {
            $service = ! empty($data['service_id']) ? Service::find($data['service_id']) : null;
            $package = ! empty($data['package_id']) ? Package::find($data['package_id']) : null;

            $customer = Customer::updateOrCreate(
                ['email' => $data['email']],
                ['name' => $data['full_name'], 'phone' => $data['phone']],
            );
            $vehicle = Vehicle::firstOrCreate([
                'customer_id' => $customer->id,
                'make' => $data['vehicle_make'],
                'model' => $data['vehicle_model'],
                'year' => $data['vehicle_year'] ?? null,
            ]);

            $data['customer_id'] = $customer->id;
            $data['vehicle_id'] = $vehicle->id;
            $data['reference'] = $this->reference();
            $data['status'] = 'PENDING';
            $data['price_snapshot'] = $package?->price ?? $service?->starting_price;

            $booking = Booking::create($data);
            BookingStatusHistory::create([
                'booking_id' => $booking->id,
                'to_status' => 'PENDING',
                'note' => 'Booking request received from the website.',
            ]);

            return $booking;
        });

        SendBookingNotification::dispatchAfterResponse($booking->id, 'created');

        return redirect()->to('/#contact')->with('booking_reference', $booking->reference);
    }

    private function reference(): string
    {
        do {
            $reference = 'VA-'.strtoupper(Str::random(8));
        } while (Booking::query()->where('reference', $reference)->exists());

        return $reference;
    }
}
