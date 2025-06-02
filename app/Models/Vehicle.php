<?php
// app/Models/Vehicle.php - Updated version

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',  // Add this
        'plate_number',
        'model',
        'capacity',
        'type',
        'status',
    ];

    public function vendor()  // Add this relationship
    {
        return $this->belongsTo(Vendor::class);
    }

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