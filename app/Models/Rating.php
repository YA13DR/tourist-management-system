<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Ratings';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'RatingID';

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
        'UserID',
        'BookingID',
        'RatingType',
        'EntityID',
        'Rating',
        'Comment',
        'RatingDate',
        'IsVisible',
        'AdminResponse'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'RatingDate' => 'datetime',
        'IsVisible' => 'boolean',
    ];

    /**
     * Get the user that owns the rating.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    /**
     * Get the booking that owns the rating.
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'BookingID', 'BookingID');
    }
}