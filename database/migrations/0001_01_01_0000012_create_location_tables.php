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
            $table->id();
            $table->string('name')->notNull();
            $table->string('code')->notNull();
            $table->string('continent_code')->nullable();
            $table->string('phone_code')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Cities table
        Schema::create('Cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('Countries', 'id');
            $table->string('name')->notNull();
            $table->boolean('isPopular')->default(false);
            $table->text('description')->nullable();
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
