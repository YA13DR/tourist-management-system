<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'Countries';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'CountryID';

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
        'CountryName',
        'CountryCode',
        'ContinentCode',
        'PhoneCode',
        'IsActive'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'IsActive' => 'boolean',
    ];

    /**
     * Get the cities for the country.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'CountryID', 'CountryID');
    }

    /**
     * Get the users for the country.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'CountryID', 'CountryID');
    }
}
