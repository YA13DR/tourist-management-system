<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Bookings';

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
        'bookingReference',
        'user_id',
        'bookingType',
        'bookingDate',
        'status',
        'totalPrice',
        'discountAmount',
        'paymentStatus',
        'specialRequests',
        'cancellationReason',
        'lastUpdated'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bookingDate' => 'datetime',
        'lastUpdated' => 'datetime',
        'totalPrice' => 'decimal:2',
        'discountAmount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the tour booking for the booking.
     */
    public function tourBooking(): HasOne
    {
        return $this->hasOne(TourBooking::class, 'booking_id', 'id');
    }

    /**
     * Get the hotel booking for the booking.
     */
    public function hotelBooking(): HasOne
    {
        return $this->hasOne(HotelBooking::class, 'booking_id', 'id');
    }

    /**
     * Get the restaurant booking for the booking.
     */
    public function restaurantBooking(): HasOne
    {
        return $this->hasOne(RestaurantBooking::class, 'booking_id', 'id');
    }

    /**
     * Get the taxi booking for the booking.
     */
    public function taxiBooking(): HasOne
    {
        return $this->hasOne(TaxiBooking::class, 'booking_id', 'id');
    }

    /**
     * Get the package booking for the booking.
     */
    public function packageBooking(): HasOne
    {
        return $this->hasOne(PackageBooking::class, 'booking_id', 'id');
    }

    /**
     * Get the payments for the booking.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'booking_id', 'id');
    }

    /**
     * Get the ratings for the booking.
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class, 'booking_id', 'id');
    }
}