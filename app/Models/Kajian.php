<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kajian extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'subtitle',
        'description',
        'datetime',
        'thumbnail',
        'is_live',
        'url',
        'status',
    ];

    public function kajianCategory(): BelongsTo
    {
        return $this->belongsTo(KajianCategory::class, 'category_id');
    }
}
