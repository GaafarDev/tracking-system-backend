<?php
// app/Models/Vendor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_registration_number',
        'contact_person',
        'email',
        'phone_number',
        'address',
        'status',
        'contract_start_date',
        'contract_end_date',
        'commission_rate',
        'notes',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'commission_rate' => 'decimal:2',
    ];

    // Relationships
    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function schedules()
    {
        return $this->hasManyThrough(Schedule::class, Driver::class);
    }

    public function incidents()
    {
        return $this->hasManyThrough(Incident::class, Driver::class);
    }

    public function sosAlerts()
    {
        return $this->hasManyThrough(SosAlert::class, Driver::class);
    }

    public function locations()
    {
        return $this->hasManyThrough(Location::class, Driver::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper methods
    public function getActiveDriversCount()
    {
        return $this->drivers()->where('status', 'active')->count();
    }

    public function getActiveVehiclesCount()
    {
        return $this->vehicles()->where('status', 'active')->count();
    }

    public function getOpenIncidentsCount()
    {
        return $this->incidents()->whereIn('incidents.status', ['reported', 'in_progress'])->count();
    }

    public function getActiveSosAlertsCount()
    {
        return $this->sosAlerts()->where('sos_alerts.status', 'active')->count();
    }
}