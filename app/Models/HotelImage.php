<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'image',
        'display_order',
        'caption',
        'is_active'
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class, 'hotel_id', 'id');
    }
}