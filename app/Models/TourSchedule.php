<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourSchedule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TourSchedules';

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
        'tour_id',
        'startDate',
        'endDate',
        'startTime',
        'availableSpots',
        'price',
        'isActive'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'startDate' => 'date',
        'endDate' => 'date',
        'startTime' => 'datetime',
        'price' => 'decimal:2',
        'isActive' => 'boolean',
    ];

    /**
     * Get the tour that owns the schedule.
     */
    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class, 'tour_id', 'id');
    }

    /**
     * Get the bookings for the schedule.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'schedule_id', 'id');
    }
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'TourActivites', 'schedule_id', 'activity_id')->withTimestamps();
    }
}