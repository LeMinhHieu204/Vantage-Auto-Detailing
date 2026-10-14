<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'eyebrow', 'short_description', 'description', 'includes', 'starting_price_cents',
        'currency', 'duration_minutes', 'consultation_required', 'meta_title', 'meta_description', 'archived_at',
        'image_url', 'image_alt', 'media_id', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'includes' => 'array', 'is_active' => 'boolean', 'consultation_required' => 'boolean',
            'starting_price_cents' => 'integer', 'duration_minutes' => 'integer', 'archived_at' => 'datetime',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 'package_services');
    }

    public function media()
    {
        return $this->belongsTo(Media::class);
    }

    public function getStartingPriceAttribute(): ?string
    {
        return $this->starting_price_cents === null ? null : '$'.number_format($this->starting_price_cents / 100, 0);
    }
}
