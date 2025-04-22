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
        Schema::create('weather_updates', function (Blueprint $table) {
            $table->id();
            $table->string('location');
            $table->enum('condition', ['clear', 'rain', 'storm', 'flood', 'fog', 'other']);
            $table->text('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('temperature', 5, 2)->nullable();
            $table->boolean('affects_routes')->default(false);
            $table->timestamp('valid_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weather_updates');
    }
};
