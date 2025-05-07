<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'VehicleTypes';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'VehicleTypeID';

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
        'TaxiServiceID',
        'TypeName',
        'Description',
        'MaxPassengers',
        'PricePerKm',
        'BasePrice',
        'ImageURL',
        'IsActive',
    ];

    /**
     * Get the taxi service that the vehicle type belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the vehicles for the vehicle type.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'VehicleTypeID', 'VehicleTypeID');
    }

    /**
     * Get the taxi bookings for the vehicle type.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'VehicleTypeID', 'VehicleTypeID');
    }
}