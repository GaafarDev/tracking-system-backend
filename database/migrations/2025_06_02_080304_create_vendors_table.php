<?php
// database/migrations/[timestamp]_create_vendors_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company_registration_number')->unique();
            $table->string('contact_person');
            $table->string('email')->unique();
            $table->string('phone_number');
            $table->text('address');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->date('contract_start_date');
            $table->date('contract_end_date')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Percentage
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};