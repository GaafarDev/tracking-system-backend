<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_location',
        'end_location',
        'description',
        'distance_km',
        'estimated_duration_minutes',
        'stops',
        'waypoints'
    ];

    protected $casts = [
        'stops' => 'array',
        'waypoints' => 'array',
        'distance_km' => 'decimal:2',
        'estimated_duration_minutes' => 'integer',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}