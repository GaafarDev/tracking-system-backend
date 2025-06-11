<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update existing data to match new enum values
        DB::table('vehicles')->where('type', 'bus')->update(['type' => 'school_bus']);
        
        // Check if there are any other invalid values and update them
        $invalidTypes = DB::table('vehicles')
            ->whereNotIn('type', ['school_bus', 'mini_bus', 'van', 'car', 'boat', 'truck', 'other'])
            ->get();
            
        // Update any remaining invalid types to 'other'
        foreach ($invalidTypes as $vehicle) {
            DB::table('vehicles')
                ->where('id', $vehicle->id)
                ->update(['type' => 'other']);
        }
        
        // Now modify the enum column
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN type ENUM('school_bus', 'mini_bus', 'van', 'car', 'boat', 'truck', 'other') NOT NULL");
    }

    public function down(): void
    {
        // First, update data back to old enum values
        DB::table('vehicles')->where('type', 'school_bus')->update(['type' => 'bus']);
        DB::table('vehicles')->where('type', 'mini_bus')->update(['type' => 'bus']);
        DB::table('vehicles')->where('type', 'truck')->update(['type' => 'other']);
        
        // Then modify the enum back to original
        DB::statement("ALTER TABLE vehicles MODIFY COLUMN type ENUM('bus', 'van', 'car', 'boat', 'other') NOT NULL");
    }
};