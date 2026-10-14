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
use Illuminate\View\View;

class AdminMediaController extends Controller
{
    public function __construct(private readonly MediaUploadService $mediaUpload, private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $query = Media::latest();
        if ($request->filled('q')) $query->where(function ($builder) use ($request) { $builder->where('original_name', 'like', '%'.$request->string('q').'%')->orWhere('alt_text', 'like', '%'.$request->string('q').'%'); });
        if ($request->filled('category')) $query->where('category', $request->string('category'));
        return view('admin.media.index', ['media' => $query->paginate(24)->withQueryString(), 'categories' => Media::query()->distinct()->orderBy('category')->pluck('category')]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240', 'dimensions:max_width=6000,max_height=6000'],
            'alt_text' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'source_note' => ['nullable', 'string', 'max:255'],
            'license_note' => ['nullable', 'string', 'max:255'],
        ]);
        $media = $this->mediaUpload->store($request->file('image'), $request->user(), $data);
        $this->auditLogger->record($request, 'media.created', $media, ['path' => $media->path]);
        return back()->with('success', 'Media uploaded to the library.');
    }

    public function update(Request $request, Media $media): RedirectResponse
    {
        $data = $request->validate([
            'alt_text' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'source_note' => ['nullable', 'string', 'max:255'],
            'license_note' => ['nullable', 'string', 'max:255'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $media->update($data + ['is_published' => (bool) ($data['is_published'] ?? false)]);
        $this->auditLogger->record($request, 'media.updated', $media);
        return back()->with('success', 'Media metadata updated.');
    }

    public function destroy(Request $request, Media $media): RedirectResponse
    {
        $used = GalleryItem::where('media_id', $media->id)->exists()
            || Service::where('media_id', $media->id)->exists()
            || BeforeAfterPair::where('before_media_id', $media->id)->orWhere('after_media_id', $media->id)->exists();
        if ($used) return back()->withErrors(['media' => 'This media is still referenced. Retire the related content before removing it.']);

        $this->mediaUpload->delete($media);
        $this->auditLogger->record($request, 'media.deleted', null, ['media_id' => $media->id]);
        return back()->with('success', 'Unused media removed.');
    }
}
