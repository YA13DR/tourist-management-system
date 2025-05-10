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
        // Travel Agencies table
        Schema::create('TravelAgencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->constrained('Locations', 'id');
            $table->decimal('averageRating', 3, 2)->default(0);
            $table->integer('totalRatings')->default(0);
            $table->string('logoURL')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('isActive')->default(true);
            $table->unsignedBigInteger('admin_id')->nullable()->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Travel Packages table
        Schema::create('TravelPackages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agency_id')->constrained('TravelAgencies', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->integer('durationDays')->notNull();
            $table->decimal('basePrice', 10, 2)->notNull();
            $table->decimal('discountPercentage', 5, 2)->default(0);
            $table->integer('maxParticipants')->nullable();
            $table->decimal('averageRating', 3, 2)->default(0);
            $table->integer('totalRatings')->default(0);
            $table->string('mainImageURL')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isFeatured')->default(false);
            $table->timestamps();
        });

        // Package Destinations table
        Schema::create('PackageDestinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('TravelPackages', 'id');
            $table->foreignId('location_id')->constrained('Locations', 'id');
            $table->integer('dayNumber')->notNull();
            $table->text('description')->nullable();
            $table->string('duration')->nullable();
            $table->timestamps();
        });

        // Package Inclusions table
        Schema::create('PackageInclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('TravelPackages', 'id');
            $table->integer('inclusionType')->notNull()->comment('1=Tour, 2=Hotel, 3=Transport, 4=Meal, 5=Other');
            $table->string('description')->notNull();
            $table->boolean('isHighlighted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PackageInclusions');
        Schema::dropIfExists('PackageDestinations');
        Schema::dropIfExists('TravelPackages');
        Schema::dropIfExists('TravelAgencies');
    }
};
