<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['customer_name', 'review_text', 'rating', 'service_name', 'source', 'published_on', 'is_placeholder', 'is_published', 'sort_order'];
    protected function casts(): array { return ['published_on' => 'date', 'is_placeholder' => 'boolean', 'is_published' => 'boolean']; }
}
