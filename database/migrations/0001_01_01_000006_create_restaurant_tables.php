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
        // Restaurants table
        Schema::create('Restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('Locations', 'id');
            $table->decimal('Latitude', 10, 7)->nullable();
            $table->decimal('Longitude', 10, 7)->nullable();
            $table->string('cuisine')->nullable();
            $table->integer('priceRange')->nullable()->comment('1=Inexpensive, 2=Moderate, 3=Expensive, 4=Very Expensive');
            $table->time('openingTime')->nullable();
            $table->time('closingTime')->nullable();
            $table->decimal('averageRating', 3, 2)->default(0);
            $table->integer('totalRatings')->default(0);
            $table->string('mainImageURL')->nullable();
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('max_tables')->nullable();
            $table->float('cost')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isFeatured')->default(false);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Restaurant Images table
        Schema::create('RestaurantImages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('Restaurants', 'id');
            $table->string('imageURL')->notNull();
            $table->integer('displayOrder')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Menu Categories table
        Schema::create('MenuCategories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('Restaurants', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->integer('displayOrder')->default(0);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Menu Items table
        Schema::create('MenuItems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('MenuCategories', 'id');
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->decimal('price', 10)->notNull();
            $table->boolean('isVegetarian')->default(false);
            $table->boolean('isVegan')->default(false);
            $table->boolean('isGlutenFree')->default(false);
            $table->integer('spiciness')->nullable()->comment('0=Not Spicy, 1=Mild, 2=Medium, 3=Hot');
            $table->string('imageURL')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isFeatured')->default(false);
            $table->timestamps();
        });

        // Restaurant Tables table
        Schema::create('RestaurantTables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained('Restaurants', 'id');
            $table->string('number')->notNull();
            $table->integer('cost')->notNull();
            $table->string('location')->nullable()->comment('Indoor, Outdoor, Private');
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('RestaurantTables');
        Schema::dropIfExists('MenuItems');
        Schema::dropIfExists('MenuCategories');
        Schema::dropIfExists('RestaurantImages');
        Schema::dropIfExists('Restaurants');
    }
};
