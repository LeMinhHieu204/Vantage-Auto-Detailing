<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'tagline', 'description', 'price_cents', 'price_label', 'currency', 'duration_minutes',
        'features', 'sort_order', 'is_featured', 'is_active', 'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price_cents' => 'integer',
            'duration_minutes' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'archived_at' => 'datetime',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'package_services');
    }

    public function getPriceAttribute(): string
    {
        return '$'.number_format($this->price_cents / 100, 0);
    }
}
