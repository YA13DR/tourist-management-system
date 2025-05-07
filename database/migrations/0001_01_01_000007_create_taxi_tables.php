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
        // Taxi Services table
        Schema::create('TaxiServices', function (Blueprint $table) {
            $table->id('TaxiServiceID');
            $table->string('ServiceName')->notNull();
            $table->text('Description')->nullable();
            $table->foreignId('LocationID')->constrained('Locations', 'id');
            $table->decimal('AverageRating', 3, 2)->default(0);
            $table->integer('TotalRatings')->default(0);
            $table->string('LogoURL')->nullable();
            $table->string('Website')->nullable();
            $table->string('Phone')->nullable();
            $table->string('Email')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->foreignId('ManagerID')->nullable()->constrained('users', 'id');
            $table->timestamps();
        });

        // Vehicle Types table
        Schema::create('VehicleTypes', function (Blueprint $table) {
            $table->id('VehicleTypeID');
            $table->foreignId('TaxiServiceID')->constrained('TaxiServices', 'TaxiServiceID');
            $table->string('TypeName')->notNull();
            $table->text('Description')->nullable();
            $table->integer('MaxPassengers')->notNull();
            $table->decimal('PricePerKm', 10, 2)->notNull();
            $table->decimal('BasePrice', 10, 2)->notNull();
            $table->string('ImageURL')->nullable();
            $table->boolean('IsActive')->default(true);
        });

        // Vehicles table
        Schema::create('Vehicles', function (Blueprint $table) {
            $table->id('VehicleID');
            $table->foreignId('TaxiServiceID')->constrained('TaxiServices', 'TaxiServiceID');
            $table->foreignId('VehicleTypeID')->constrained('VehicleTypes', 'VehicleTypeID');
            $table->string('RegistrationNumber')->notNull();
            $table->string('Model')->nullable();
            $table->integer('Year')->nullable();
            $table->string('Color')->nullable();
            $table->boolean('IsActive')->default(true);
        });

        // Drivers table
        Schema::create('Drivers', function (Blueprint $table) {
            $table->id('DriverID');
            $table->foreignId('UserID')->constrained('users', 'id');
            $table->foreignId('TaxiServiceID')->constrained('TaxiServices', 'TaxiServiceID');
            $table->string('LicenseNumber')->notNull();
            $table->integer('ExperienceYears')->nullable();
            $table->decimal('Rating', 3, 2)->default(0);
            $table->boolean('IsActive')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Drivers');
        Schema::dropIfExists('Vehicles');
        Schema::dropIfExists('VehicleTypes');
        Schema::dropIfExists('TaxiServices');
    }
};
