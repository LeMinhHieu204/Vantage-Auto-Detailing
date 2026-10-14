<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_admin_can_create_service_and_package(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.services.store'), [
            'name' => 'Test Service', 'short_description' => 'Short', 'description' => 'Long description',
            'includes' => "Wash\nProtect", 'currency' => 'USD', 'starting_price' => '125', 'sort_order' => 1, 'is_active' => '1',
        ])->assertRedirect(route('admin.services.index'));
        $service = Service::where('slug', 'test-service')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.packages.store'), [
            'name' => 'Test Package', 'tagline' => 'A package', 'price' => '250', 'price_label' => 'Starting at',
            'currency' => 'USD', 'features' => "One\nTwo", 'service_ids' => [$service->id], 'sort_order' => 1, 'is_active' => '1',
        ])->assertRedirect(route('admin.packages.index'));

        $this->assertDatabaseHas('services', ['slug' => 'test-service', 'starting_price_cents' => 12500]);
        $this->assertDatabaseHas('packages', ['slug' => 'test-package', 'price_cents' => 25000]);
        $this->assertDatabaseHas('package_services', ['service_id' => $service->id]);
        $this->actingAs($admin)->get(route('admin.services.index'))->assertOk()->assertSee('Test Service');
        $this->actingAs($admin)->get(route('admin.packages.index'))->assertOk()->assertSee('Test Package');
    }

    public function test_admin_can_upload_media_and_create_review(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        $this->actingAs($admin)->post(route('admin.media.store'), [
            'image' => UploadedFile::fake()->image('studio.jpg', 1200, 800),
            'alt_text' => 'A detailed vehicle', 'category' => 'Gallery', 'license_note' => 'Owned',
        ])->assertRedirect();
        $this->assertDatabaseCount('media', 1);
        $this->assertTrue(Storage::disk('public')->exists(Media::first()->path));

        $this->actingAs($admin)->post(route('admin.reviews.store'), [
            'customer_name' => 'Real Customer', 'review_text' => 'Excellent work.', 'rating' => 5,
            'service_name' => 'Exterior Detailing', 'source' => 'Email', 'sort_order' => 1,
        ])->assertRedirect(route('admin.reviews.index'));
        $review = Review::where('customer_name', 'Real Customer')->firstOrFail();
        $this->assertFalse($review->is_published);

        $this->actingAs($admin)->patch(route('admin.reviews.visibility', $review))->assertRedirect();
        $this->assertDatabaseHas('reviews', ['id' => $review->id, 'is_published' => true]);
        $this->actingAs($admin)->get(route('admin.reviews.index'))->assertOk()->assertSee('Real Customer');
    }

    public function test_settings_update_is_persisted_and_publicly_used(): void
    {
        $this->actingAs($this->admin())->put(route('admin.settings.update'), [
            'settings' => [
                'hero_heading' => "Updated Heading\nSecond Line", 'hero_description' => 'Updated description',
                'primary_cta' => 'Request a slot', 'about_text' => 'Updated about', 'about_supporting' => 'Updated support',
                'business_name' => 'Vantage Test', 'contact_phone' => '555', 'contact_email' => 'hello@example.com',
                'location_label' => 'By appointment', 'hours' => 'Weekdays', 'footer_note' => 'Updated footer',
                'site_title' => 'Vantage Test', 'site_description' => 'Updated SEO', 'booking_enabled' => '1',
                'minimum_advance_days' => '1', 'maximum_booking_days' => '60',
            ],
        ])->assertRedirect();

        $this->get('/')->assertOk()->assertSee('Updated Heading')->assertSee('Request a slot');
    }
}
