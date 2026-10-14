<?php

namespace App\Http\Requests;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:180'],
            'phone' => ['required', 'string', 'max:40'],
            'vehicle_make' => ['required', 'string', 'max:80'],
            'vehicle_model' => ['required', 'string', 'max:80'],
            'vehicle_year' => ['nullable', 'string', 'max:4'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'privacy_consent' => ['accepted'],
            'website' => ['nullable', 'size:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'privacy_consent.accepted' => 'Please confirm that you have read the privacy notice.',
            'website.size' => 'Unable to process this request.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (SiteSetting::get('booking_enabled', '1') === '0') {
                $validator->errors()->add('preferred_date', 'Online booking requests are temporarily unavailable.');
                return;
            }

            if (! $this->filled('preferred_date')) return;
            $date = Carbon::parse($this->input('preferred_date'));
            $minimum = max(0, (int) SiteSetting::get('minimum_advance_days', '0'));
            $maximum = max($minimum, (int) SiteSetting::get('maximum_booking_days', '365'));
            if ($date->lt(today()->addDays($minimum))) {
                $validator->errors()->add('preferred_date', "Please choose a date at least {$minimum} day(s) ahead.");
            }
            if ($date->gt(today()->addDays($maximum))) {
                $validator->errors()->add('preferred_date', "Please choose a date within the next {$maximum} day(s).");
            }
        });
    }
}
