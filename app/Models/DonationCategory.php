<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationCategory extends Model
{
    public $timestamps = false; // Matikan timestamps

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
