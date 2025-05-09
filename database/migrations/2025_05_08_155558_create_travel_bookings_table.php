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
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->constrained('Bookings', 'id');
            
            $table->unsignedBigInteger('user_id')->constrained('user','id')->onDelete('cascade');
            $table->unsignedBigInteger('flight_id')->constrained('travel_flights','id')->onDelete('cascade');
            $table->integer('number_of_people')->default(1);
            $table->date('booking_date');
            $table->double('total_price');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_bookings');
    }
};
