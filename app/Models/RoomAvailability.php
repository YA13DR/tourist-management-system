<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAvailability extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'RoomAvailability';

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
        'roomType_id',
        'date',
        'availableRooms',
        'price',
        'isBlocked'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:2',
        'isBlocked' => 'boolean',
    ];

    /**
     * Get the room type that owns the availability.
     */
    public function roomType(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'RoomTypeID', 'id');
    }
}