<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function donatureLists(): HasMany
    {
        return $this->hasMany(DonatureList::class);
    }

    // Accessor untuk menghitung total donasi
    public function getTotalCollectedAttribute(): int
    {
        return $this->donatureLists()->sum('total_donation');
    }

    // Accessor untuk cek apakah target tercapai
    public function getIsTargetReachedAttribute(): bool
    {
        return $this->total_collected >= $this->target_amount;
    }
}
