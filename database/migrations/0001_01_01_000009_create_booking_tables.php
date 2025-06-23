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
        // Bookings table
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique()->notNull();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->enum('booking_type', ['tour', 'hotel', 'taxi', 'restaurant', 'package'])->notNull();
            $table->dateTime('booking_date')->default(now());
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->decimal('total_price', 10, 2)->notNull();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('travel_bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->constrained('Bookings', 'id');
            $table->unsignedBigInteger('user_id')->constrained('user','id')->onDelete('cascade');
            $table->unsignedBigInteger('flight_id')->constrained('travel_flights','id')->onDelete('cascade');
            $table->enum('ticket_type', ['one_way', 'round_trip'])->default('one_way');
            $table->integer('number_of_people')->default(1);
            $table->date('booking_date');
            $table->double('total_price');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
        // Tour Bookings table
        Schema::create('tour_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('tour_id')->constrained('tours', 'id');
            $table->foreignId('schedule_id')->constrained('tour_schedules', 'id');
            $table->integer('number_of_adults')->notNull()->default(1);
            $table->integer('number_of_children')->notNull()->default(0);
            $table->double('cost')->notNull();
            $table->timestamps();
        });

        // Hotel Bookings table
        Schema::create('hotel_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->foreignId('hotel_id')->constrained('hotels', 'id');
            $table->foreignId('room_type_id')->constrained('room_types', 'id');
            $table->integer('hotel_room');
            $table->date('check_in_date')->notNull();
            $table->date('check_out_date')->notNull();
            $table->integer('number_of_rooms')->notNull()->default(1);
            $table->integer('number_of_guests')->notNull();
            $table->double('cost')->notNull();
            $table->timestamps();

        });

        // Restaurant Bookings table
        Schema::create('restaurant_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->integer('table_id');
            $table->json('order')->nullable();
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('number_of_guests');
            $table->string('location')->nullable();
            $table->decimal('cost')->nullable();
            $table->timestamps();
        });

        // Taxi Bookings table
        Schema::create('taxi_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings', 'id');
            $table->foreignId('taxi_service_id')->constrained('TaxiServices', 'TaxiServiceID');
            $table->foreignId('vehicle_type_id')->constrained('VehicleTypes', 'VehicleTypeID');
            $table->foreignId('pickup_location_id')->constrained('locations', 'id');
            $table->foreignId('dropoff_location_id')->constrained('locations', 'id');
            $table->dateTime('pickup_dateTime')->notNull();
            $table->decimal('estimated_distance', 10, 2)->nullable();
            $table->foreignId('driver_id')->nullable()->constrained('Drivers', 'DriverID');
            $table->foreignId('vehicle_id')->nullable()->constrained('Vehicles', 'VehicleID');
            $table->timestamps();
        });

        // Package Bookings table
        Schema::create('package_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('travel_packages', 'id');
            $table->integer('number_of_adults')->notNull()->default(1);
            $table->integer('number_of_children')->notNull()->default(0);
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('travel_bookings');
        Schema::dropIfExists('package_bookings');
        Schema::dropIfExists('taxi_bookings');
        Schema::dropIfExists('restaurant_bookings');
        Schema::dropIfExists('hotel_bookings');
        Schema::dropIfExists('tour_bookings');
        Schema::dropIfExists('bookings');
    }
};
