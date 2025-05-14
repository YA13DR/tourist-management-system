<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'MenuItems';

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
        'category_id',
        'name',
        'description',
        'price',
        'imageURL',
        'isVegetarian',
        'isVegan',
        'isGlutenFree',
        'spiciness',
        'isAvailable',
        'isPopular',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'isVegetarian' => 'boolean',
        'isVegan' => 'boolean',
        'isGlutenFree' => 'boolean',
        'spiciness' => 'integer',
        'isAvailable' => 'boolean',
        'isPopular' => 'boolean',
    ];

    /**
     * Get the category that owns the menu item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id', 'id');
    }
    // public function restaurant(): BelongsTo
    // {
    //     return $this->belongsTo(Restaurant::class);
    // }
}