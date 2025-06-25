<?php
// app/Models/Driver.php - Updated version

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',  // Add this
        'user_id',
        'license_number',
        'phone_number',
        'address',
        'status',
    ];

    public function vendor()  // Add this relationship
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class, 'schedules');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    public function sosAlerts()
    {
        return $this->hasMany(SosAlert::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}