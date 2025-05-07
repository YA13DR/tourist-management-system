<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Cities';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CityID';

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
        'CountryID',
        'CityName',
        'IsPopular',
        'Description'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'IsPopular' => 'boolean',
    ];

    /**
     * Get the country that owns the city.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'CountryID', 'CountryID');
    }

    /**
     * Get the locations for the city.
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class, 'CityID', 'CityID');
    }
}
