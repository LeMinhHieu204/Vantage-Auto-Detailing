<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::withCount('bookings')->latest();
        if ($request->filled('q')) $query->where(function ($builder) use ($request) { $term = $request->string('q'); $builder->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")->orWhere('phone', 'like', "%{$term}%"); });
        return view('admin.customers.index', ['customers' => $query->paginate(20)->withQueryString()]);
    }

    public function show(Customer $customer): View
    {
        $customer->load(['vehicles', 'bookings.service', 'bookings.package']);
        return view('admin.customers.show', compact('customer'));
    }
}
