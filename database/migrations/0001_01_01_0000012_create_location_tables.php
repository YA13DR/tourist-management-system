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
        // Countries table
        Schema::create('Countries', function (Blueprint $table) {
            $table->id('CountryID');
            $table->string('CountryName')->notNull();
            $table->string('CountryCode')->notNull();
            $table->string('ContinentCode')->nullable();
            $table->string('PhoneCode')->nullable();
            $table->boolean('IsActive')->default(true);
            $table->timestamps();
        });

        // Cities table
        Schema::create('Cities', function (Blueprint $table) {
            $table->id('CityID');
            $table->foreignId('CountryID')->constrained('Countries', 'CountryID');
            $table->string('CityName')->notNull();
            $table->boolean('IsPopular')->default(false);
            $table->text('Description')->nullable();
            $table->timestamps();
        });

        // Locations table
        Schema::create('Locations', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->decimal('Latitude', 10, 7)->nullable();
            $table->decimal('Longitude', 10, 7)->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->boolean('IsPopular')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Locations');
        Schema::dropIfExists('Cities');
        Schema::dropIfExists('Countries');
    }
};
