<?php

namespace App\Http\Controllers;

use App\Models\BeforeAfterPair;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\Service;
use App\Services\AuditLogger;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminGalleryController extends Controller
{
    public function __construct(private readonly MediaUploadService $mediaUpload, private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        return view('admin.gallery.index', [
            'items' => GalleryItem::with('media')->orderBy('sort_order')->paginate(15, ['*'], 'items_page'),
            'pairs' => BeforeAfterPair::with(['beforeMedia', 'afterMedia', 'service'])->orderBy('sort_order')->paginate(15, ['*'], 'pairs_page'),
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', 'string', 'max:80'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240', 'dimensions:max_width=6000,max_height=6000'],
            'image_alt' => ['required', 'string', 'max:180'],
            'source_note' => ['nullable', 'string', 'max:255'],
            'license_note' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);
        $item = DB::transaction(function () use ($request, $data) {
            $media = $this->mediaUpload->store($request->file('image'), $request->user(), [
                'alt_text' => $data['image_alt'], 'category' => 'Gallery', 'source_note' => $data['source_note'] ?? null, 'license_note' => $data['license_note'] ?? null,
            ]);
            return GalleryItem::create([
                'media_id' => $media->id, 'title' => $data['title'], 'category' => $data['category'],
                'image_url' => $media->url, 'image_alt' => $data['image_alt'], 'source_note' => $data['source_note'] ?? null,
                'license_note' => $data['license_note'] ?? null, 'is_demo' => false, 'is_published' => false, 'sort_order' => $data['sort_order'],
            ]);
        });
        $this->auditLogger->record($request, 'gallery.created', $item);
        return back()->with('success', 'Gallery item uploaded as unpublished.');
    }

    public function toggle(Request $request, GalleryItem $galleryItem): RedirectResponse
    {
        $galleryItem->update(['is_published' => ! $galleryItem->is_published]);
        $this->auditLogger->record($request, 'gallery.visibility_changed', $galleryItem);
        return back()->with('success', 'Gallery visibility updated.');
    }

    public function storePair(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', 'string', 'max:80'],
            'vehicle_label' => ['nullable', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'service_id' => ['nullable', 'exists:services,id'],
            'before_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240', 'dimensions:max_width=6000,max_height=6000'],
            'after_image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240', 'dimensions:max_width=6000,max_height=6000'],
        ]);
        $pair = DB::transaction(function () use ($request, $data) {
            $before = $this->mediaUpload->store($request->file('before_image'), $request->user(), ['alt_text' => $data['title'].' before', 'category' => 'Before & After']);
            $after = $this->mediaUpload->store($request->file('after_image'), $request->user(), ['alt_text' => $data['title'].' after', 'category' => 'Before & After']);
            return BeforeAfterPair::create([
                'service_id' => $data['service_id'] ?? null, 'before_media_id' => $before->id, 'after_media_id' => $after->id,
                'title' => $data['title'], 'category' => $data['category'], 'vehicle_label' => $data['vehicle_label'] ?? null,
                'description' => $data['description'] ?? null, 'is_published' => false,
            ]);
        });
        $this->auditLogger->record($request, 'before_after.created', $pair);
        return back()->with('success', 'Before/after pair uploaded as unpublished.');
    }

    public function togglePair(Request $request, BeforeAfterPair $pair): RedirectResponse
    {
        $pair->update(['is_published' => ! $pair->is_published]);
        $this->auditLogger->record($request, 'before_after.visibility_changed', $pair);
        return back()->with('success', 'Before/after pair visibility updated.');
    }
}
