<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate_number',
        'model',
        'capacity',
        'type',
        'status',
    ];

    public function drivers()
    {
        return $this->belongsToMany(Driver::class, 'schedules');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}