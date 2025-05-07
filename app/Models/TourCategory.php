<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TourCategory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'TourCategories';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

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
        'name',
        'description',
        'parentCategory_id',
        'iconURL',
        'displayOrder',
        'isActive'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'isActive' => 'boolean',
    ];

    /**
     * Get the parent category.
     */
    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'parentCategory_id', 'id');
    }

    /**
     * Get the child categories.
     */
    public function childCategories(): HasMany
    {
        return $this->hasMany(TourCategory::class, 'parentCategory_id', 'id');
    }

    /**
     * Get the tours for the category.
     */
    public function tours()
    {
        return $this->belongsToMany(
            Tour::class,
            'TourCategoryMapping',
            'category_id',
            'tour_id'
        );
    }
}