<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    public const STATUSES = [
        'PENDING', 'CONTACTED', 'CONFIRMED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED', 'NO_SHOW',
    ];

    protected $fillable = [
        'reference', 'status', 'customer_id', 'vehicle_id', 'full_name', 'email', 'phone', 'vehicle_make', 'vehicle_model',
        'vehicle_year', 'service_id', 'package_id', 'preferred_date', 'preferred_time', 'appointment_date', 'appointment_time',
        'notes', 'internal_notes', 'reschedule_reason', 'price_snapshot', 'contacted_at', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'appointment_date' => 'date',
            'contacted_at' => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function customer() { return $this->belongsTo(Customer::class); }
    public function vehicle() { return $this->belongsTo(Vehicle::class); }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function history()
    {
        return $this->hasMany(BookingStatusHistory::class);
    }

    public function canTransitionTo(string $next): bool
    {
        if ($next === $this->status) return true;
        return in_array($next, [
            'PENDING' => ['CONTACTED', 'CONFIRMED', 'CANCELLED'],
            'CONTACTED' => ['CONFIRMED', 'CANCELLED'],
            'CONFIRMED' => ['IN_PROGRESS', 'CANCELLED', 'NO_SHOW'],
            'IN_PROGRESS' => ['COMPLETED', 'CANCELLED', 'NO_SHOW'],
            'COMPLETED' => [],
            'CANCELLED' => [],
            'NO_SHOW' => [],
        ][$this->status] ?? [], true);
    }
}
