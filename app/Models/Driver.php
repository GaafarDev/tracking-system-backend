<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'license_number',
        'phone_number',
        'address',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'schedules');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function sosAlerts()
    {
        return $this->hasMany(SosAlert::class);
    }
}