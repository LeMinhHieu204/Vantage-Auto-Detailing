<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public const FIELDS = [
        'business_name' => ['label' => 'Business name', 'type' => 'text'],
        'tagline' => ['label' => 'Tagline', 'type' => 'text'],
        'contact_phone' => ['label' => 'Contact phone', 'type' => 'text'],
        'contact_email' => ['label' => 'Contact email', 'type' => 'email'],
        'location_label' => ['label' => 'Location / address', 'type' => 'text'],
        'map_link' => ['label' => 'Google Maps link', 'type' => 'url'],
        'hours' => ['label' => 'Operating hours', 'type' => 'textarea'],
        'hero_heading' => ['label' => 'Hero heading', 'type' => 'text'],
        'hero_description' => ['label' => 'Hero description', 'type' => 'textarea'],
        'primary_cta' => ['label' => 'Primary CTA label', 'type' => 'text'],
        'about_text' => ['label' => 'About text', 'type' => 'textarea'],
        'about_supporting' => ['label' => 'About supporting text', 'type' => 'textarea'],
        'footer_note' => ['label' => 'Footer description', 'type' => 'textarea'],
        'site_title' => ['label' => 'SEO site title', 'type' => 'text'],
        'site_description' => ['label' => 'SEO description', 'type' => 'textarea'],
        'booking_enabled' => ['label' => 'Booking requests enabled', 'type' => 'boolean'],
        'minimum_advance_days' => ['label' => 'Minimum advance notice (days)', 'type' => 'number'],
        'maximum_booking_days' => ['label' => 'Maximum booking window (days)', 'type' => 'number'],
    ];

    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(): View
    {
        $values = SiteSetting::query()->whereIn('key', array_keys(self::FIELDS))->pluck('value', 'key');
        return view('admin.settings.index', ['fields' => self::FIELDS, 'values' => $values]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['settings' => ['required', 'array']]);
        foreach (self::FIELDS as $key => $definition) {
            $value = $data['settings'][$key] ?? ($definition['type'] === 'boolean' ? '0' : '');
            if ($definition['type'] === 'email' && $value !== '') validator(['value' => $value], ['value' => 'email'])->validate();
            if ($definition['type'] === 'url' && $value !== '') validator(['value' => $value], ['value' => 'url'])->validate();
            SiteSetting::updateOrCreate(['key' => $key], ['type' => $definition['type'], 'value' => is_bool($value) ? ($value ? '1' : '0') : (string) $value, 'is_public' => true]);
        }
        $this->auditLogger->record($request, 'settings.updated', null, ['keys' => array_keys(self::FIELDS)]);
        return back()->with('success', 'Website settings saved.');
    }
}
