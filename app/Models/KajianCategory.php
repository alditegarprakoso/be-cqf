<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KajianCategory extends Model
{
    public $timestamps = false; // Matikan timestamps

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];

    public function kajians(): HasMany
    {
        return $this->hasMany(Kajian::class);
    }
}
