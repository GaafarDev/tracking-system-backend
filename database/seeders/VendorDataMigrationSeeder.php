<?php
// database/seeders/VendorDataMigrationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Vendor;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Route;

class VendorDataMigrationSeeder extends Seeder
{
    public function run(): void
    {
        // Create a default vendor for existing data
        $defaultVendor = Vendor::create([
            'name' => 'Legacy Vendor',
            'company_registration_number' => 'DEFAULT001',
            'contact_person' => 'Admin User',
            'email' => 'legacy@maralinear.com',
            'phone_number' => '+60123456789',
            'address' => 'Kuala Lumpur, Malaysia',
            'status' => 'active',
            'contract_start_date' => now()->subYear(),
            'contract_end_date' => now()->addYear(),
            'commission_rate' => 15.00,
            'notes' => 'Default vendor created during system migration',
        ]);

        // Assign all existing drivers to the default vendor
        Driver::whereNull('vendor_id')->update(['vendor_id' => $defaultVendor->id]);

        // Assign all existing vehicles to the default vendor
        Vehicle::whereNull('vendor_id')->update(['vendor_id' => $defaultVendor->id]);

        // Assign all existing routes to the default vendor
        Route::whereNull('vendor_id')->update(['vendor_id' => $defaultVendor->id]);

        $this->command->info('Successfully migrated existing data to default vendor');
    }
}