<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make vendor_id required in drivers table
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable(false)->change();
        });

        // Make vendor_id required in vehicles table
        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable(false)->change();
        });

        // Make vendor_id required in routes table
        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->change();
        });

        Schema::table('vehicles', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->change();
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->change();
        });
    }
};