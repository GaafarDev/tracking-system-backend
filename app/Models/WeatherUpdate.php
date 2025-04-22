<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'condition',
        'description',
        'latitude',
        'longitude',
        'temperature',
        'affects_routes',
        'valid_until',
    ];

    protected $casts = [
        'affects_routes' => 'boolean',
        'valid_until' => 'datetime',
    ];
}