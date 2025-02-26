<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'target_amount',
        'bank_account',
        'status',
        'thumbnail',
    ];
}
