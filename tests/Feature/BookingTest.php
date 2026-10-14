<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_booking_request_is_persisted(): void
    {
        $service = Service::create([
            'name' => 'Exterior Detailing', 'slug' => 'exterior-detailing', 'short_description' => 'A clean.',
            'includes' => ['Wash'], 'is_active' => true,
        ]);

        $response = $this->from('/#contact')->post('/booking', [
            'full_name' => 'Alex Driver',
            'email' => 'alex@example.com',
            'phone' => '555 123 4567',
            'vehicle_make' => 'Porsche',
            'vehicle_model' => '911',
            'service_id' => $service->id,
            'preferred_date' => now()->addDay()->toDateString(),
            'privacy_consent' => '1',
            'website' => '',
        ]);

        $response->assertRedirect('/#contact');
        $this->assertDatabaseHas('bookings', ['full_name' => 'Alex Driver', 'status' => 'PENDING']);
        $this->assertDatabaseCount('booking_status_history', 1);
    }

    public function test_invalid_booking_is_rejected_and_csrf_is_required(): void
    {
        $this->post('/booking', [])->assertSessionHasErrors(['full_name', 'email', 'privacy_consent']);
        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_admin_routes_require_an_admin_user(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_update_booking_status(): void
    {
        $booking = Booking::create([
            'reference' => 'VA-TEST1234', 'status' => 'PENDING', 'full_name' => 'Taylor Customer',
            'email' => 'taylor@example.com', 'phone' => '555 000 0000', 'vehicle_make' => 'Audi',
            'vehicle_model' => 'A4', 'preferred_date' => now()->addDay()->toDateString(),
        ]);
        $admin = User::factory()->create(['is_admin' => true, 'password' => Hash::make('a-long-test-password')]);

        $this->actingAs($admin)->patch(route('admin.bookings.update', $booking), ['status' => 'CONFIRMED'])
            ->assertRedirect();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'CONFIRMED']);
        $this->assertDatabaseHas('booking_status_history', ['booking_id' => $booking->id, 'to_status' => 'CONFIRMED']);
    }

    public function test_admin_operational_pages_render(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin);

        foreach ([
            route('admin.dashboard'), route('admin.bookings'), route('admin.services.index'), route('admin.services.create'),
            route('admin.packages.index'), route('admin.packages.create'), route('admin.media.index'), route('admin.gallery.index'),
            route('admin.reviews.index'), route('admin.settings.index'), route('admin.customers.index'), route('admin.audit.index'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_confirmed_appointment_slots_cannot_conflict(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $existing = Booking::create([
            'reference' => 'VA-EXIST123', 'status' => 'CONFIRMED', 'full_name' => 'Existing Customer', 'email' => 'existing@example.com',
            'phone' => '555 111 1111', 'vehicle_make' => 'BMW', 'vehicle_model' => '3 Series', 'appointment_date' => '2026-11-10', 'appointment_time' => '10:00 AM',
        ]);
        $candidate = Booking::create([
            'reference' => 'VA-CAND123', 'status' => 'PENDING', 'full_name' => 'Candidate Customer', 'email' => 'candidate@example.com',
            'phone' => '555 222 2222', 'vehicle_make' => 'Audi', 'vehicle_model' => 'A4',
        ]);

        $this->actingAs($admin)->patch(route('admin.bookings.update', $candidate), [
            'status' => 'CONFIRMED', 'appointment_date' => '2026-11-10', 'appointment_time' => '10:00 AM',
        ])->assertSessionHasErrors('status');

        $this->assertDatabaseHas('bookings', ['id' => $existing->id, 'status' => 'CONFIRMED']);
        $this->assertDatabaseHas('bookings', ['id' => $candidate->id, 'status' => 'PENDING']);
    }
}
