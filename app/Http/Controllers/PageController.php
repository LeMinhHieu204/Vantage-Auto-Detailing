<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\GalleryItem;
use Illuminate\View\View;

class PageController extends Controller
{
    public function gallery(): View
    {
        return view('pages.gallery', ['gallery' => GalleryItem::where('is_published', true)->orderBy('sort_order')->get()]);
    }

    public function booking(string $reference): View
    {
        return view('pages.booking', ['booking' => Booking::where('reference', $reference)->firstOrFail()]);
    }

    public function legal(string $page): View
    {
        abort_unless(in_array($page, ['privacy-policy', 'terms-of-service'], true), 404);
        return view('pages.legal', ['page' => $page]);
    }
}
