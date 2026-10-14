<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function show(string $slug): View
    {
        $service = Service::query()->where('slug', $slug)->where('is_active', true)->whereNull('archived_at')->firstOrFail();
        return view('services.show', compact('service'));
    }
}
