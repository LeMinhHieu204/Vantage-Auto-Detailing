<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Services\AuditLogger;
use App\Services\MediaUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminServiceController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger, private readonly MediaUploadService $mediaUpload) {}

    public function index(): View
    {
        $services = Service::withCount('bookings')->orderBy('sort_order')->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create(): View
    {
        return view('admin.services.form', ['service' => new Service(), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $service = DB::transaction(fn () => $this->save(new Service(), $data, $request));
        $this->auditLogger->record($request, 'service.created', $service);
        return redirect()->route('admin.services.index')->with('success', "Service {$service->name} created.");
    }

    public function edit(Service $service): View
    {
        return view('admin.services.form', ['service' => $service, 'mode' => 'edit']);
    }

    public function update(Request $request, Service $service): RedirectResponse
    {
        $data = $this->validated($request, $service);
        $service = DB::transaction(fn () => $this->save($service, $data, $request));
        $this->auditLogger->record($request, 'service.updated', $service);
        return redirect()->route('admin.services.index')->with('success', "Service {$service->name} updated.");
    }

    public function destroy(Request $request, Service $service): RedirectResponse
    {
        $service->update(['is_active' => false, 'archived_at' => now()]);
        $this->auditLogger->record($request, 'service.archived', $service);
        return back()->with('success', "Service {$service->name} archived without removing booking history.");
    }

    public function toggle(Request $request, Service $service): RedirectResponse
    {
        $service->update(['is_active' => ! $service->is_active]);
        $this->auditLogger->record($request, 'service.visibility_changed', $service, ['is_active' => $service->is_active]);
        return back()->with('success', 'Service visibility updated.');
    }

    private function validated(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('services', 'slug')->ignore($service?->id)],
            'eyebrow' => ['nullable', 'string', 'max:80'],
            'short_description' => ['required', 'string', 'max:300'],
            'description' => ['required', 'string', 'max:5000'],
            'includes' => ['nullable', 'string', 'max:3000'],
            'starting_price' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'consultation_required' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:160'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:10240', 'dimensions:max_width=6000,max_height=6000'],
            'image_alt' => ['nullable', 'string', 'max:180'],
        ]);
    }

    private function save(Service $service, array $data, Request $request): Service
    {
        $service->fill([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'eyebrow' => $data['eyebrow'] ?? null,
            'short_description' => $data['short_description'],
            'description' => $data['description'],
            'includes' => collect(preg_split('/\r\n|\r|\n/', $data['includes'] ?? ''))->map(fn ($value) => trim($value))->filter()->values()->all(),
            'starting_price_cents' => isset($data['starting_price']) ? (int) round(((float) $data['starting_price']) * 100) : null,
            'currency' => strtoupper($data['currency']),
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'consultation_required' => (bool) ($data['consultation_required'] ?? false),
            'sort_order' => $data['sort_order'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
            'image_alt' => $data['image_alt'] ?? $service->image_alt,
            'archived_at' => null,
        ]);
        $service->save();

        if ($request->hasFile('image')) {
            $media = $this->mediaUpload->store($request->file('image'), $request->user(), [
                'alt_text' => $data['image_alt'] ?? $service->name,
                'category' => 'Service',
            ]);
            $service->update(['media_id' => $media->id, 'image_url' => $media->url]);
        }

        return $service->refresh();
    }
}
