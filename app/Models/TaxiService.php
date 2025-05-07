<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxiService extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TaxiServices';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'TaxiServiceID';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ServiceName',
        'Description',
        'LocationID',
        'AverageRating',
        'TotalRatings',
        'LogoURL',
        'Website',
        'Phone',
        'Email',
        'IsActive',
        'ManagerID',
    ];

    /**
     * Get the location that the taxi service belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'LocationID', 'LocationID');
    }

    /**
     * Get the manager that owns the taxi service.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'ManagerID', 'UserID');
    }

    /**
     * Get the vehicle types for the taxi service.
     */
    public function vehicleTypes()
    {
        return $this->hasMany(VehicleType::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the vehicles for the taxi service.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the drivers for the taxi service.
     */
    public function drivers()
    {
        return $this->hasMany(Driver::class, 'TaxiServiceID', 'TaxiServiceID');
    }

    /**
     * Get the taxi bookings for the taxi service.
     */
    public function taxiBookings()
    {
        return $this->hasMany(TaxiBooking::class, 'TaxiServiceID', 'TaxiServiceID');
    }
}