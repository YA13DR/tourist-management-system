<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxiBooking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TaxiBookings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'TaxiBookingID';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'BookingID',
        'TaxiServiceID',
        'VehicleTypeID',
        'PickupLocationID',
        'DropoffLocationID',
        'PickupDateTime',
        'EstimatedDistance',
        'DriverID',
        'VehicleID'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'PickupDateTime' => 'datetime',
        'EstimatedDistance' => 'decimal:2',
    ];

    /**
     * Get the booking that owns the taxi booking.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'BookingID', 'BookingID');
    }

    /**
     * Get the pickup location that owns the taxi booking.
     */
    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'PickupLocationID', 'LocationID');
    }

    /**
     * Get the dropoff location that owns the taxi booking.
     */
    public function dropoffLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'DropoffLocationID', 'LocationID');
    }
}