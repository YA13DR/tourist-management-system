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
        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('Bookings', 'id');
            $table->string('payment_reference')->unique()->notNull();
            $table->decimal('amount', 10, 2)->notNull();
            $table->dateTime('paymentDate')->default(now());
            $table->integer('paymentMethod')->notNull()->comment('1=Credit Card, 2=PayPal, 3=Bank Transfer');
            $table->string('transaction_id')->nullable();
            $table->integer('status')->default(1)->comment('1=Pending, 2=Success, 3=Failed, 4=Refunded');
            $table->text('gateway_response')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->dateTime('refund_date')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();
        });

        // Ratings table
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->integer('rating_type')->notNull()->comment('1=Tour, 2=Hotel, 3=Taxi, 4=Restaurant, 5=Package, 6=Guide, 7=Driver');
            $table->integer('entity_id')->notNull()->comment('tour_id, hotel_id, taxiService_id, restaurant_id, package_id, guide_id, driver_id');
            $table->integer('rating')->notNull();
            $table->text('comment')->nullable();
            $table->dateTime('ratingdate')->default(now());
            $table->boolean('isVisible')->default(true);
            $table->text('admin_response')->nullable();
            $table->unique(['user_id','rating_type']);
            $table->timestamps();
        });

        // Feedback table
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id');
            $table->text('feedback_text')->notNull();
            $table->dateTime('feedback_date')->default(now());
            $table->integer('feedback_type')->notNull()->comment('1=App, 2=Service, 3=Other');
            $table->integer('status')->default(1)->comment('1=Unread, 2=Read, 3=Responded');
            $table->text('response_text')->nullable();
            $table->dateTime('response_date')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users', 'id');
            $table->timestamps();
        });

        // Promotions table
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promotion_code')->unique()->notNull();
            $table->text('description')->nullable();
            $table->integer('discount_type')->notNull()->comment('1=Percentage, 2=Fixed Amount');
            $table->decimal('discount_value', 10, 2)->notNull();
            $table->decimal('minimum_purchase', 10, 2)->default(0);
            $table->dateTime('start_date')->notNull();
            $table->dateTime('end_date')->notNull();
            $table->integer('usage_limit')->nullable();
            $table->integer('current_usage')->default(0);
            $table->integer('applicable_type')->nullable()->comment('1=All, 2=Tour, 3=Hotel, 4=Taxi, 5=Restaurant, 6=Package ,7=Flight');
            $table->boolean('isActive')->default(true);
            $table->unsignedBigInteger('created_by')->constrained('admins', 'id');
            $table->timestamps();
        });


        // User Ranks table
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['Starter', 'Bronze', 'Silver', 'Gold', 'Platinum'])->nullable();
            $table->integer('min_points'); 
            $table->timestamps();
        });

        Schema::create('user_ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');            
            $table->foreignId('rank_id')->nullable()->constrained('ranks')->nullOnDelete();
            $table->integer('points_earned')->default(0);
            $table->timestamps();
        });
       
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('action'); 
            $table->integer('points'); 
            $table->timestamps();
        });
        Schema::create('discount_points', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['book_flight', 'book_tour', 'book_hotel','add_restaurant_order', 'book_restaurant']);
            $table->integer('required_points');
            $table->decimal('discount_percentage', 5, 2); 
            $table->timestamps();
        });
        
        // Wishlist table
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'id');
            $table->integer('item_type')->notNull()->comment('1=Tour, 2=Hotel, 3=Restaurant, 4=Package');
            $table->integer('item_id')->notNull();
            $table->dateTime('added_date')->default(now());
            $table->unique(['user_id', 'item_type', 'item_id']);
            $table->timestamps();
        });

        // Audit Log table
        Schema::create('auditLog', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users', 'id');
            $table->string('entity_type')->notNull();
            $table->integer('entity_id')->notNull();
            $table->string('action')->notNull();
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->dateTime('log_date')->default(now());
            $table->timestamps();
        });
        // Tour Translations table
        Schema::create('TourTranslations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('Tours', 'id');
            $table->string('languageCode');
            $table->text('translatedDescription');
            $table->timestamps();
        });

        // Partnerships table
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users', 'id');
            $table->foreignId('hotel_id')->nullable()->constrained('Hotels', 'id');
            $table->foreignId('restaurant_id')->nullable()->constrained('Restaurants', 'id');
            $table->foreignId('taxiService_id')->nullable()->constrained('TaxiServices', 'TaxiServiceID');
            $table->decimal('discount_percentage', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('Partnerships');
        Schema::dropIfExists('TourTranslations');
        Schema::dropIfExists('UserRanks');
        Schema::dropIfExists('AuditLog');
        Schema::dropIfExists('UserSessions');
        Schema::dropIfExists('Notifications');
        Schema::dropIfExists('Wishlist');
        Schema::dropIfExists('Promotions');
        Schema::dropIfExists('Feedback');
        Schema::dropIfExists('Ratings');
        Schema::dropIfExists('Payments');
    }
};
