<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id',
        'driver_id',
        'vehicle_id',
        'departure_time',
        'arrival_time',
        'day_of_week',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}