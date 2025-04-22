<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Check if columns don't exist before adding them
            if (!Schema::hasColumn('drivers', 'user_id')) {
                $table->foreignId('user_id')->constrained();
            }
            
            if (!Schema::hasColumn('drivers', 'license_number')) {
                $table->string('license_number');
            }
            
            if (!Schema::hasColumn('drivers', 'phone_number')) {
                $table->string('phone_number');
            }
            
            if (!Schema::hasColumn('drivers', 'address')) {
                $table->string('address')->nullable();
            }
            
            if (!Schema::hasColumn('drivers', 'status')) {
                $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            }
        });
    }
    
    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            // Only drop foreign key if column exists
            if (Schema::hasColumn('drivers', 'user_id')) {
                $table->dropForeign(['user_id']);
            }
            
            // Only drop columns that exist
            $columns = ['user_id', 'license_number', 'phone_number', 'address', 'status'];
            $columnsToRemove = [];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('drivers', $column)) {
                    $columnsToRemove[] = $column;
                }
            }
            
            if (!empty($columnsToRemove)) {
                $table->dropColumn($columnsToRemove);
            }
        });
    }
};
