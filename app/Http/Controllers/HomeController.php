<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use App\Models\BeforeAfterPair;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\SiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $services = Service::query()->where('is_active', true)->whereNull('archived_at')->orderBy('sort_order')->get();
        $packages = Package::query()->where('is_active', true)->whereNull('archived_at')->orderBy('sort_order')->get();
        $gallery = GalleryItem::query()->where('is_published', true)->orderBy('sort_order')->get();
        $reviews = Review::query()->where('is_published', true)->where('is_placeholder', false)->orderBy('sort_order')->latest('published_on')->get();
        $beforeAfter = BeforeAfterPair::query()->with(['beforeMedia', 'afterMedia', 'service'])->where('is_published', true)->orderBy('sort_order')->first();

        $settings = SiteSetting::query()->where('is_public', true)->pluck('value', 'key');

        return view('home', compact('services', 'packages', 'gallery', 'reviews', 'beforeAfter', 'settings'));
    }
}
