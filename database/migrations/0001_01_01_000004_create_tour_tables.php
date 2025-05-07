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
        // Tour Categories table
        Schema::create('TourCategories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->foreignId('parentCategory_id')->nullable()->constrained('TourCategories', 'id')->cascadeOnDelete();
            $table->string('iconURL')->nullable();
            $table->integer('displayOrder')->default(0);
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Tours table
        Schema::create('Tours', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->text('description')->nullable();
            $table->string('shortDescription')->nullable();
            $table->foreignId('location_id')->constrained('Locations', 'id');
            $table->decimal('durationHours', 5, 2)->nullable();
            $table->integer('durationDays')->nullable();
            $table->decimal('basePrice', 10, 2)->notNull();
            $table->decimal('discountPercentage', 5, 2)->default(0);
            $table->integer('maxCapacity')->notNull();
            $table->integer('minParticipants')->default(1);
            $table->integer('difficultyLevel')->default(1)->comment('1=Easy, 2=Moderate, 3=Difficult');
            $table->decimal('averageRating', 3, 2)->default(0);
            $table->integer('totalRatings')->default(0);
            $table->string('mainImageURL')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isFeatured')->default(false);
            $table->unsignedBigInteger('admin_id')->constrained('admins', 'id')->cascadeOnDelete();
            $table->timestamps();
        });

        // Tour Images table
        Schema::create('TourImages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('Tours', 'id');
            $table->string('imageURL')->notNull();
            $table->integer('displayOrder')->default(0);
            $table->string('caption')->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });

        // Tour Category Mapping table
        Schema::create('TourCategoryMapping', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('Tours', 'id');
            $table->foreignId('category_id')->constrained('TourCategories', 'id');
            $table->timestamps();
        });

        // Tour Schedules table
        Schema::create('TourSchedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('Tours', 'id');
            $table->date('startDate')->notNull();
            $table->date('endDate')->nullable();
            $table->time('startTime')->nullable();
            $table->integer('availableSpots')->notNull();
            $table->decimal('price', 10, 2)->nullable();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
        
        Schema::create('activites', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notNull();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
        Schema::create('TourActivites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('TourSchedules', 'id');
            $table->foreignId('activity_id')->constrained('activites', 'id');
            $table->boolean('isActive')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('TourSchedules');
        Schema::dropIfExists('TourCategoryMapping');
        Schema::dropIfExists('TourImages');
        Schema::dropIfExists('Tours');
        Schema::dropIfExists('TourCategories');
    }
};
