<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Vehicles';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'VehicleID';

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
        'VehicleTypeID',
        'RegistrationNumber',
        'Model',
        'Year',
        'Color',
        'IsActive',
    ];

    /**
     * Get the taxi service that the vehicle belongs to.
     */
    public function taxiService()
    {
        return $this->belongsTo(TaxiService::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the vehicle type that the vehicle belongs to.
     */
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'VehicleTypeID', 'VehicleTypeID');
    }

    /**
     * Get the taxi bookings for the vehicle.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'VehicleID', 'VehicleID');
    }
}