<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'waypoints',
        'stops',
        'distance_km',
        'estimated_duration_minutes',
    ];

    protected $casts = [
        'waypoints' => 'array',
        'stops' => 'array',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}