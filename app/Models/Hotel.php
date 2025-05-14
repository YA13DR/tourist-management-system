<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Hotel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Hotels';

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
        'discount',
        'starRating',
        'checkInTime',
        'checkOutTime',
        'averageRating',
        'totalRatings',
        'mainImageURL',
        'website',
        'phone',
        'email',
        'isActive',
        'isFeatured',
        'admin_id',
        'Latitude',
        'Longitude',
        'city',
        'country'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checkInTime' => 'datetime',
        'checkOutTime' => 'datetime',
        'averageRating' => 'decimal:2',
        'isActive' => 'boolean',
        'isFeatured' => 'boolean',
    ];

    /**
     * Get the manager that owns the hotel.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * Get the images for the hotel.
     */
    public function images(): HasMany
    {
        return $this->hasMany(HotelImage::class, 'hotel_id', 'id');
    }

    /**
     * Get the room types for the hotel.
     */
    public function roomTypes(): HasMany
    {
        return $this->hasMany(RoomType::class, 'hotel_id', 'id');
    }

    /**
     * Get the amenities for the hotel.
     */
    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(
            HotelAmenity::class,
            'HotelAmenityMapping',
            'HotelID',
            'AmenityID',
            'HotelID',
            'AmenityID'
        );
    }

}