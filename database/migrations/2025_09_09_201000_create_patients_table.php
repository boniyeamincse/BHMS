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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hospital_id');
            $table->string('name');
            $table->date('date_of_birth')->nullable();
            $table->enum('type', ['OPD', 'IPD'])->default('OPD'); // Out Patient Department / In Patient Department
            $table->date('admission_date')->nullable();
            $table->date('discharge_date')->nullable();
            $table->enum('status', ['active', 'discharged', 'deceased'])->default('active');
            $table->unsignedBigInteger('ward_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('hospital_id')->references('id')->on('hospitals')->onDelete('cascade');
            // Remove ward_id FK for now, add after wards table is created
            $table->index(['hospital_id', 'type']);
            $table->index(['hospital_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};