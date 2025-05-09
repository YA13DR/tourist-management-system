<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tour extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Tours';

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
        'shortDescription',
        'location_id',
        'durationHours',
        'durationDays',
        'basePrice',
        'discountPercentage',
        'maxCapacity',
        'minParticipants',
        'difficultyLevel',
        'averageRating',
        'totalRatings',
        'mainImageURL',
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
        'durationHours' => 'decimal:2',
        'basePrice' => 'decimal:2',
        'discountPercentage' => 'decimal:2',
        'averageRating' => 'decimal:2',
        'isActive' => 'boolean',
        'isFeatured' => 'boolean',
    ];

    /**
     * Get the location that owns the tour.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
    public function package(): BelongsTo
    {
        return $this->belongsTo(TravelPackage::class, 'tour_id', 'id');
    }

    /**
     * Get the user that created the tour.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * Get the images for the tour.
     */
    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class, 'tour_id', 'id');
    }

    /**
     * Get the schedules for the tour.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'tour_id', 'id')->where('isActive', true);
    }

    /**
     * Get the translations for the tour.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(TourTranslation::class, 'tour_id', 'id');
    }

    /**
     * Get the categories for the tour.
     */
    public function categories()
{
    return $this->belongsToMany(
        TourCategory::class, // الموديل المرتبط
        'TourCategoryMapping', // اسم جدول الربط
        'tour_id', // المفتاح الأجنبي لهذا الموديل في جدول الربط
        'category_id' // المفتاح الأجنبي للطرف الآخر في جدول الربط
    );
}

    /**
     * Get the bookings for the tour.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'tour_id', 'id');
    }
}