<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonatureList extends Model
{
    protected $fillable = [
        'donation_id',
        'donature_name',
        'email',
        'phone',
        'total_donation',
        'info',
        'status',
        'attachment',
    ];

    public function donation(): BelongsTo
    {
        return $this->belongsTo(Donation::class, 'donation_id');
    }
}
