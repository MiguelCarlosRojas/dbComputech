<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetectionLog extends Model
{
    protected $fillable = [
        'category',
        'label',
        'display_name',
        'confidence',
        'color',
        'details',
    ];

    protected $casts = [
        'confidence' => 'float',
        'details' => 'array',
        'created_at' => 'datetime',
    ];
}
