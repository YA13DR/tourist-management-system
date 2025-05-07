<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Restaurants';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'location_id',
        'longitude',
        'latitude',
        'cuisine',
        'priceRange',
        'openingTime',
        'closingTime',
        'averageRating',
        'totalRatings',
        'mainImageURL',
        'website',
        'phone',
        'email',
        'hasReservation',
        'isActive',
        'isFeatured',
        'admin_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'openingTime' => 'datetime',
        'closingTime' => 'datetime',
        'averageRating' => 'decimal:2',
        'hasReservation' => 'boolean',
        'isActive' => 'boolean',
        'isFeatured' => 'boolean',
    ];

    /**
     * Get the location that owns the restaurant.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'LocationID');
    }

    /**
     * Get the admin that owns the restaurant.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * Get the images for the restaurant.
     */
    public function images(): HasMany
    {
        return $this->hasMany(RestaurantImage::class, 'restaurant_id', 'id');
    }

    /**
     * Get the menu categories for the restaurant.
     */
    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id', 'id');
    }

    /**
     * Get the tables for the restaurant.
     */
    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class, 'restaurant_id', 'id');
    }

    /**
     * Get the bookings for the restaurant.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(RestaurantBooking::class, 'restaurant_id', 'id');
    }
}