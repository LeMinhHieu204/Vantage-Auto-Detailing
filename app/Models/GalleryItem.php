<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = ['media_id', 'title', 'category', 'image_url', 'image_alt', 'source_note', 'license_note', 'is_demo', 'is_published', 'sort_order'];
    protected function casts(): array { return ['is_demo' => 'boolean', 'is_published' => 'boolean']; }

    public function media() { return $this->belongsTo(Media::class); }
}
