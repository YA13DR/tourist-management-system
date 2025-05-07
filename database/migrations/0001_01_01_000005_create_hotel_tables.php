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
        // Hotels table
        Schema::create('Hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->integer('starRating')->nullable();
            $table->time('checkInTime')->nullable();
            $table->time('checkOutTime')->nullable();
            $table->decimal('averageRating', 3, 2)->default(0);
            $table->integer('totalRatings')->default(0);
            $table->string('mainImageURL')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isFeatured')->default(false);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Hotel Images table
        Schema::create('HotelImages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('Hotels', 'id');
            $table->string('imageURL')->notNull();
            $table->integer('displayOrder')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Hotel Amenities table
        Schema::create('HotelAmenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->string('iconURL')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Hotel Amenity Mapping table
        Schema::create('HotelAmenityMapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('Hotels', 'id');
            $table->foreignId('amenity_id')->constrained('HotelAmenities', 'id');
            $table->unique(['hotel_id', 'amenity_id']);
            $table->timestamps();
        });

        // Room Types table
        Schema::create('RoomTypes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('Hotels', 'id');
            $table->string('name')->notNull();
            $table->integer('number');
            $table->text('description')->nullable();
            $table->integer('maxOccupancy')->notNull();
            $table->decimal('basePrice', 10, 2)->notNull();
            $table->decimal('discountPercentage', 5, 2)->default(0);
            $table->string('size')->nullable();
            $table->string('bedType')->nullable();
            $table->string('imageURL')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Room Availability table
        Schema::create('RoomAvailability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roomType_id')->constrained('RoomTypes', 'id');
            $table->date('date')->notNull();
            $table->integer('availableRooms')->notNull();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('isBlocked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RoomAvailability');
        Schema::dropIfExists('RoomTypes');
        Schema::dropIfExists('HotelAmenityMapping');
        Schema::dropIfExists('HotelAmenities');
        Schema::dropIfExists('HotelImages');
        Schema::dropIfExists('Hotels');
    }
};
