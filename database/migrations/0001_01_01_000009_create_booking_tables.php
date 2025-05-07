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
        Schema::create('Bookings', function (Blueprint $table) {
            $table->id();
            $table->string('bookingReference')->unique()->notNull();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->integer('bookingType')->notNull()->comment('1=Tour, 2=Hotel, 3=Taxi, 4=Restaurant, 5=Package');
            $table->dateTime('bookingDate')->default(now());
            $table->integer('status')->default(1)->comment('1=Pending, 2=Confirmed, 3=Cancelled, 4=Completed');
            $table->decimal('totalPrice', 10, 2)->notNull();
            $table->decimal('discountAmount', 10, 2)->default(0);
            $table->integer('paymentStatus')->default(1)->comment('1=Pending, 2=Paid, 3=Refunded, 4=Failed');
            $table->text('specialRequests')->nullable();
            $table->text('cancellationReason')->nullable();
            $table->timestamps();
        });

        // Tour Bookings table
        Schema::create('TourBookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('Bookings', 'id');
            $table->foreignId('tour_id')->constrained('Tours', 'id');
            $table->foreignId('schedule_id')->constrained('TourSchedules', 'id');
            $table->integer('numberOfAdults')->notNull()->default(1);
            $table->integer('numberOfChildren')->notNull()->default(0);
            $table->double('cost')->notNull();
            $table->timestamps();
        });

        // Hotel Bookings table
        Schema::create('HotelBookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('Bookings', 'id');
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->foreignId('hotel_id')->constrained('Hotels', 'id');
            $table->foreignId('roomType_id')->constrained('RoomTypes', 'id');
            $table->integer('hotelRoom');
            $table->date('checkInDate')->notNull();
            $table->date('checkOutDate')->notNull();
            $table->integer('numberOfRooms')->notNull()->default(1);
            $table->integer('numberOfGuests')->notNull();
            $table->double('cost')->notNull();
            $table->timestamps();

        });

        // Restaurant Bookings table
        Schema::create('RestaurantBookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('Bookings', 'id');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->integer('table_id');
            $table->json('order')->nullable();
            $table->date('reservationDate');
            $table->time('reservationTime');
            $table->integer('numberOfGuests');
            $table->string('location')->nullable();
            $table->decimal('cost')->nullable();
            $table->timestamps();
        });

        // Taxi Bookings table
        Schema::create('TaxiBookings', function (Blueprint $table) {
            $table->id('TaxiBookingID');
            $table->foreignId('BookingID')->constrained('Bookings', 'id');
            $table->foreignId('TaxiServiceID')->constrained('TaxiServices', 'TaxiServiceID');
            $table->foreignId('VehicleTypeID')->constrained('VehicleTypes', 'VehicleTypeID');
            $table->foreignId('PickupLocationID')->constrained('Locations', 'id');
            $table->foreignId('DropoffLocationID')->constrained('Locations', 'id');
            $table->dateTime('PickupDateTime')->notNull();
            $table->decimal('EstimatedDistance', 10, 2)->nullable();
            $table->foreignId('DriverID')->nullable()->constrained('Drivers', 'DriverID');
            $table->foreignId('VehicleID')->nullable()->constrained('Vehicles', 'VehicleID');
            $table->timestamps();
        });

        // Package Bookings table
        Schema::create('PackageBookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('Bookings', 'id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('TravelPackages', 'id');
            $table->foreignId('agency_id')->nullable()->constrained('TravelAgencies')->onDelete('set null');
            $table->integer('numberOfAdults')->notNull()->default(1);
            $table->integer('numberOfChildren')->notNull()->default(0);
            $table->decimal('cost', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PackageBookings');
        Schema::dropIfExists('TaxiBookings');
        Schema::dropIfExists('RestaurantBookings');
        Schema::dropIfExists('HotelBookings');
        Schema::dropIfExists('TourBookings');
        Schema::dropIfExists('Bookings');
    }
};
