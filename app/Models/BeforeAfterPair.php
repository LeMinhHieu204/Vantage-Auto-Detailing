<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeforeAfterPair extends Model
{
    protected $fillable = ['service_id', 'before_media_id', 'after_media_id', 'title', 'category', 'vehicle_label', 'description', 'is_published', 'sort_order'];

    protected function casts(): array
    {
        return ['is_published' => 'boolean'];
    }

    public function service() { return $this->belongsTo(Service::class); }
    public function beforeMedia() { return $this->belongsTo(Media::class, 'before_media_id'); }
    public function afterMedia() { return $this->belongsTo(Media::class, 'after_media_id'); }
}
