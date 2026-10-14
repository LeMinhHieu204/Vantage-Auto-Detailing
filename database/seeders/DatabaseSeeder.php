<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $services = [
            ['name' => 'Exterior Detailing', 'slug' => 'exterior-detailing', 'eyebrow' => 'CLEAN / PROTECT', 'short_description' => 'A careful exterior reset for paintwork, wheels and glass.', 'description' => 'A measured, hand-finished clean that removes road film, brake dust and surface contamination while preserving the finish you already have.', 'includes' => ['Exterior hand wash', 'Wheel and tire cleaning', 'Surface decontamination', 'Exterior finishing'], 'image_url' => 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Technician washing a dark vehicle', 'sort_order' => 1],
            ['name' => 'Interior Detailing', 'slug' => 'interior-detailing', 'eyebrow' => 'RESET / REFRESH', 'short_description' => 'A deeper clean for the places you spend your drive.', 'description' => 'We work through the touch points, surfaces and fabrics that make a cabin feel fresh again—from the dash and console to seats, mats and glass.', 'includes' => ['Interior vacuuming', 'Dashboard and console cleaning', 'Seat and trim care', 'Interior glass cleaning'], 'image_url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Clean vehicle interior with dark leather seats', 'sort_order' => 2],
            ['name' => 'Paint Correction', 'slug' => 'paint-correction', 'eyebrow' => 'REFINE / RESTORE', 'short_description' => 'Reduce swirls and refine the finish with measured polishing.', 'description' => 'Paint correction starts with inspection. We choose a conservative process to improve clarity and gloss without promising to erase every imperfection.', 'includes' => ['Paint inspection', 'Swirl reduction', 'Machine polishing', 'Finish enhancement'], 'image_url' => 'https://images.unsplash.com/photo-1613214150384-8d33d4e8aef0?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Detailer polishing a car panel', 'sort_order' => 3],
            ['name' => 'Ceramic Coating', 'slug' => 'ceramic-coating', 'eyebrow' => 'PREPARE / PROTECT', 'short_description' => 'Long-term gloss and easier maintenance, tailored to your car.', 'description' => 'Coating performance depends on the product, preparation, application and maintenance plan. We walk through the right option before work begins.', 'includes' => ['Paint preparation', 'Coating application', 'Curing information', 'Maintenance guidance'], 'image_url' => 'https://images.unsplash.com/photo-1625047509248-ec889cbff17f?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Glossy black car hood reflecting light', 'sort_order' => 4],
            ['name' => 'Paint Protection Film', 'slug' => 'paint-protection-film', 'eyebrow' => 'GUARD / PRESERVE', 'short_description' => 'Talk through partial, full-front or full-body protection.', 'description' => 'PPF coverage and pricing depend on the vehicle, material and finish. Start with a consultation and we will scope the right coverage for your driving.', 'includes' => ['PPF consultation', 'Partial coverage', 'Full-front coverage', 'Full-body options'], 'image_url' => 'https://images.unsplash.com/photo-1530046339914-8e9b1cc4b8b9?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Close detail of a polished vehicle front end', 'sort_order' => 5],
            ['name' => 'Maintenance Detailing', 'slug' => 'maintenance-detailing', 'eyebrow' => 'KEEP / READY', 'short_description' => 'Scheduled upkeep that keeps a finished car feeling cared for.', 'description' => 'Regular maintenance is the easiest way to protect the work already done. Choose a rhythm that fits your vehicle, commute and storage.', 'includes' => ['Routine vehicle care', 'Interior refresh', 'Exterior maintenance', 'Scheduled upkeep'], 'image_url' => 'https://images.unsplash.com/photo-1605164599901-5b7c3da64ed5?auto=format&fit=crop&w=1200&q=84', 'image_alt' => 'Detailing tools and a clean vehicle surface', 'sort_order' => 6],
        ];
        foreach ($services as $service) {
            Service::updateOrCreate(['slug' => $service['slug']], $service);
        }

        $packages = [
            ['name' => 'Essential Clean', 'slug' => 'essential-clean', 'tagline' => 'For a tidy, well-kept daily driver.', 'price_cents' => 9900, 'price_label' => 'Starting at', 'features' => ['Exterior hand wash', 'Interior vacuum', 'Window cleaning', 'Wheel and tire cleaning', 'Basic finishing'], 'sort_order' => 1],
            ['name' => 'Premium Detail', 'slug' => 'premium-detail', 'tagline' => 'A more complete reset inside and out.', 'price_cents' => 19900, 'price_label' => 'Starting at', 'features' => ['Everything in Essential', 'Deep interior cleaning', 'Paint decontamination', 'Additional exterior protection', 'Interior conditioning'], 'sort_order' => 2, 'is_featured' => true],
            ['name' => 'Ultimate Protection', 'slug' => 'ultimate-protection', 'tagline' => 'Preparation for a longer-term protection plan.', 'price_cents' => 39900, 'price_label' => 'Starting at', 'features' => ['Everything in Premium', 'Paint enhancement (1–2 steps)', 'Protective treatment consultation', 'Enhanced interior care'], 'sort_order' => 3],
        ];
        foreach ($packages as $package) {
            Package::updateOrCreate(['slug' => $package['slug']], $package);
        }

        $gallery = [
            ['title' => 'Paint correction demo', 'category' => 'Paint Correction', 'image_url' => 'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?auto=format&fit=crop&w=1600&q=86', 'image_alt' => 'Black vehicle being washed inside a detailing studio', 'source_note' => 'Unsplash demo image; replace with a verified same-vehicle pair before launch.', 'sort_order' => 1],
            ['title' => 'Interior detailing', 'category' => 'Interior Detailing', 'image_url' => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1000&q=86', 'image_alt' => 'Vehicle interior with clean leather seats', 'source_note' => 'Unsplash demo image.', 'sort_order' => 2],
            ['title' => 'Wheel detailing', 'category' => 'Wheel Detailing', 'image_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1000&q=86', 'image_alt' => 'Close view of a performance car wheel and bodywork', 'source_note' => 'Unsplash demo image.', 'sort_order' => 3],
            ['title' => 'Ceramic coating finish', 'category' => 'Ceramic Coating', 'image_url' => 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&w=1000&q=86', 'image_alt' => 'Glossy dark car exterior in soft light', 'source_note' => 'Unsplash demo image.', 'sort_order' => 4],
        ];
        foreach ($gallery as $item) {
            GalleryItem::updateOrCreate(['title' => $item['title']], $item + ['is_demo' => true, 'is_published' => true]);
        }

        Review::updateOrCreate(['customer_name' => 'Development placeholder'], [
            'review_text' => 'Real customer feedback will appear here once approved in the admin dashboard.',
            'service_name' => 'Placeholder', 'rating' => null, 'is_placeholder' => true, 'is_published' => true,
        ]);

        $settings = [
            'business_name' => 'Vantage Auto Detailing',
            'contact_email' => 'hello@vantageautodetailing.example',
            'contact_phone' => 'Add verified phone number',
            'location_label' => 'Service location shared when your request is confirmed',
            'hours' => "Mon–Sat · 8:00 AM–6:00 PM\nSunday · Closed",
            'hero_heading' => "A Cleaner Car.\nA Better Drive.",
            'hero_description' => 'Professional auto detailing and protection services to keep your vehicle looking its best, inside and out.',
            'primary_cta' => 'Book an Appointment',
            'about_text' => 'At Vantage Auto Detailing, we believe every vehicle deserves proper care. Our services focus on cleaning, protecting and maintaining cars with the attention to detail their owners expect.',
            'about_supporting' => 'From routine exterior detailing to paint correction and ceramic coating, we help drivers protect their vehicles and enjoy a cleaner, more comfortable driving experience.',
            'booking_enabled' => '1',
            'minimum_advance_days' => '1',
            'maximum_booking_days' => '90',
            'site_title' => 'Vantage Auto Detailing',
            'site_description' => 'Professional auto detailing, paint correction and vehicle protection services.',
            'footer_note' => 'Sample content — replace contact details, reviews and gallery proof with verified business data before launch.',
        ];
        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
