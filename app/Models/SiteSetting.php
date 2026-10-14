<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'type', 'value', 'is_public'];

    protected function casts(): array { return ['is_public' => 'boolean']; }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->where('is_public', true)->value('value') ?? $default;
    }
}
