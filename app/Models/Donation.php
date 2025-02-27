<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function donationCategory(): BelongsTo
    {
        return $this->belongsTo(DonationCategory::class, 'category_id');
    }
}
