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
            $table->foreignId('booking_id')->constrained('bookings');
            $table->string('payment_reference')->unique();
            $table->decimal('amount', 10, 2);
            $table->dateTime('payment_date')->default(now());
            $table->enum('payment_method', ['credit_card', 'paypal', 'bank_transfer']);
            $table->string('transaction_id')->nullable();
            $table->enum('status', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->text('gateway_response')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->dateTime('refund_date')->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();
        });

        // Ratings table
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('rating_type', ['tour', 'hotel', 'taxi', 'restaurant', 'package', 'guide', 'driver']);
            $table->unsignedBigInteger('entity_id');
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->dateTime('rating_date')->default(now());
            $table->boolean('is_visible')->default(true);
            $table->text('admin_response')->nullable();
            $table->unique(['user_id', 'rating_type']);
            $table->timestamps();
        });

        // Feedback table
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->text('feedback_text');
            $table->dateTime('feedback_date')->default(now());
            $table->enum('feedback_type', ['app', 'service', 'other']);
            $table->enum('status', ['unread', 'read', 'responded'])->default('unread');
            $table->text('response_text')->nullable();
            $table->dateTime('response_date')->nullable();
            $table->foreignId('responded_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        // Promotions table
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('promotion_code')->unique();
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 10, 2);
            $table->decimal('minimum_purchase', 10, 2)->default(0);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('usage_limit')->nullable();
            $table->integer('current_usage')->default(0);
            $table->enum('applicable_type', ['all', 'tour', 'hotel', 'taxi', 'restaurant', 'package', 'flight'])->nullable();
            $table->boolean('is_active')->default(true);
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
            $table->enum('action', ['book_flight', 'book_tour', 'book_hotel', 'add_restaurant_order', 'book_restaurant']);
            $table->integer('points'); 
            $table->timestamps();
        });
        Schema::create('discount_points', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['book_flight', 'book_tour', 'book_hotel', 'add_restaurant_order', 'book_restaurant']);
            $table->integer('required_points');
            $table->decimal('discount_percentage', 5, 2);
            $table->timestamps();
        });
        
        // Wishlist table
        Schema::create('wishlist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('item_type', ['tour', 'hotel', 'restaurant', 'package']);
            $table->unsignedBigInteger('item_id');
            $table->dateTime('added_date')->default(now());
            $table->unique(['user_id', 'item_type', 'item_id']);
            $table->timestamps();
        });

        // Audit Log table
        Schema::create('audit_log', function (Blueprint $table) {
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
        Schema::create('tour_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours');
            $table->string('language_code');
            $table->text('translated_description');
            $table->timestamps();
        });

        // Partnerships table
        Schema::create('partnerships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->foreignId('hotel_id')->nullable()->constrained('hotels');
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants');
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
        Schema::dropIfExists('partnerships');
        Schema::dropIfExists('tour_translations');
        Schema::dropIfExists('user_ranks');
        Schema::dropIfExists('audit_log');
        Schema::dropIfExists('wishlist');
        Schema::dropIfExists('promotions');
        Schema::dropIfExists('feedback');
        Schema::dropIfExists('ratings');
        Schema::dropIfExists('payments');
    }
};
