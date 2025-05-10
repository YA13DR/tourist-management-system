<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelPackage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TravelPackages';

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
        'agency_id',
        'name',
        'description',
        'durationDays',
        'basePrice',
        'discountPercentage',
        'maxParticipants',
        'averageRating',
        'totalRatings',
        'mainImageURL',
        'isActive',
        'isFeatured'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'durationDays' => 'integer',
        'basePrice' => 'decimal:2',
        'discountPercentage' => 'decimal:2',
        'maxParticipants' => 'integer',
        'averageRating' => 'decimal:2',
        'totalRatings' => 'integer',
        'isActive' => 'boolean',
        'isFeatured' => 'boolean',
    ];

    /**
     * Get the agency that owns the package.
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(TravelAgency::class, 'agency_id', 'id');
    }
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }

    /**
     * Get the destinations for the package.
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(PackageDestination::class, 'package_id', 'id');
    }

    /**
     * Get the inclusions for the package.
     */
    public function inclusions(): HasMany
    {
        return $this->hasMany(PackageInclusion::class, 'package_id', 'id');
    }

    /**
     * Get the bookings for the package.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(PackageBooking::class, 'package_id', 'id');
    }
}