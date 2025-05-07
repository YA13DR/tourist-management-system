<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelBooking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'HotelBookings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'booking_id',
        'user_id',
        'hotel_id',
        'roomType_id',
        'hotelRoom',
        'checkInDate',
        'checkOutDate',
        'numberOfRooms',
        'numberOfGuests',
        'numberOfRooms',
        'cost'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checkInDate' => 'date',
        'checkOutDate' => 'date',
    ];

    /**
     * Get the hotel that owns the hotel booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    /**
     * Get the room type that owns the hotel booking.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'roomType_id', 'id');
    }
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'id');
    }
}