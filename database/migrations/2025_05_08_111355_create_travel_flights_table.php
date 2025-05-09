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
        Schema::create('travel_flights', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->constrained('TravelAgencies','id')->onDelete('cascade');
            $table->string('flight_number')->unique();
            $table->unsignedBigInteger('departure_id')->constrained('Locations')->onDelete('cascade');
            $table->unsignedBigInteger('arrival_id')->constrained('Locations')->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('duration_minutes')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('available_seats');
            $table->enum('status', ['scheduled', 'delayed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_flights');
    }
};
