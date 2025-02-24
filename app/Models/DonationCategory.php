<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonationCategory extends Model
{
    public $timestamps = false; // Matikan timestamps

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status',
    ];
}
