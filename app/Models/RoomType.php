<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RoomTypes';

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
        'hotel_id',
        'name',
        'number',
        'description',
        'maxOccupancy',
        'basePrice',
        'discountPercentage',
        'size',
        'bedType',
        'imageURL',
        'isActive'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'basePrice' => 'decimal:2',
        'discountPercentage' => 'decimal:2',
        'isActive' => 'boolean',
    ];

    /**
     * Get the hotel that owns the room type.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }

    /**
     * Get the availability for the room type.
     */
    public function availability(): HasMany
    {
        return $this->hasMany(RoomAvailability::class, 'roomType_id', 'id');
    }

    /**
     * Get the bookings for the room type.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(HotelBooking::class, 'roomType_id', 'id');
    }
}