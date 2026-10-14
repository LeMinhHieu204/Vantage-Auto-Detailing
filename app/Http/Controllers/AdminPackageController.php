<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Service;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPackageController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        $packages = Package::withCount('bookings')->orderBy('sort_order')->paginate(15);
        return view('admin.packages.index', compact('packages'));
    }

    public function create(): View
    {
        return view('admin.packages.form', ['package' => new Package(), 'services' => Service::where('is_active', true)->orderBy('name')->get(), 'mode' => 'create']);
    }

    public function store(Request $request): RedirectResponse
    {
        $package = DB::transaction(fn () => $this->save(new Package(), $request));
        $this->auditLogger->record($request, 'package.created', $package);
        return redirect()->route('admin.packages.index')->with('success', "Package {$package->name} created.");
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.form', ['package' => $package->load('services'), 'services' => Service::where('is_active', true)->orderBy('name')->get(), 'mode' => 'edit']);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $package = DB::transaction(fn () => $this->save($package, $request));
        $this->auditLogger->record($request, 'package.updated', $package);
        return redirect()->route('admin.packages.index')->with('success', "Package {$package->name} updated.");
    }

    public function destroy(Request $request, Package $package): RedirectResponse
    {
        $package->update(['is_active' => false, 'archived_at' => now()]);
        $this->auditLogger->record($request, 'package.archived', $package);
        return back()->with('success', "Package {$package->name} archived.");
    }

    private function save(Package $package, Request $request): Package
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:140', Rule::unique('packages', 'slug')->ignore($package->id)],
            'tagline' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
            'price_label' => ['required', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'duration_minutes' => ['nullable', 'integer', 'min:0', 'max:1440'],
            'features' => ['nullable', 'string', 'max:3000'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:services,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:9999'],
            'is_featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $package->fill([
            'name' => $data['name'],
            'slug' => ($data['slug'] ?? null) ?: Str::slug($data['name']),
            'tagline' => $data['tagline'] ?? null,
            'description' => $data['description'] ?? null,
            'price_cents' => (int) round(((float) $data['price']) * 100),
            'price_label' => $data['price_label'],
            'currency' => strtoupper($data['currency']),
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'features' => collect(preg_split('/\r\n|\r|\n/', $data['features'] ?? ''))->map(fn ($value) => trim($value))->filter()->values()->all(),
            'sort_order' => $data['sort_order'],
            'is_featured' => (bool) ($data['is_featured'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
            'archived_at' => null,
        ])->save();

        if ($package->is_featured) {
            Package::where('id', '<>', $package->id)->update(['is_featured' => false]);
        }
        $package->services()->sync($data['service_ids'] ?? []);
        return $package->refresh();
    }
}
