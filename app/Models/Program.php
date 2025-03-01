<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'description',
        'status',
        'thumbnail',
    ];
}
