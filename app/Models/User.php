<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable
{
       /** @use HasFactory<\Database\Factories\UserFactory> */
       use HasFactory, Notifiable,HasApiTokens;

       /**
        * The attributes that are mass assignable.
        *
        * @var array<int, string>
        */
       protected $fillable = [
           'photo',
           'first_name',
           'last_name',
           'phone_number',
           'location',
           'password',
           'code',
           'expire_at',
           'email',
           'email_verified_at',
       ];
   
     
       /**
        * The attributes that should be hidden for serialization.
        *
        * @var array<int, string>
        */
       protected $hidden = [
           'password',
           'remember_token',
       ];
   
       /**
        * Get the attributes that should be cast.
        *
        * @return array<string, string>
        */
       protected function casts(): array
       {
           return [
               'email_verified_at' => 'datetime',
               'password' => 'hashed',
           ];
       }

    /**
     * Get the country that owns the user.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'CountryID', 'CountryID');
    }

    public function rank()
    {
        return $this->hasOne(UserRank::class);
    }
    public function createdTours(): HasMany
    {
        return $this->hasMany(Tour::class, 'CreatedBy', 'UserID');
    }

    /**
     * Get the bookings for the user.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'UserID', 'UserID');
    }
    public function favoriteTours()
    {
        return $this->morphedByMany(Tour::class, 'favoritable', 'favorites');
    }

    public function favoriteHotels()
    {
        return $this->morphedByMany(Hotel::class, 'favoritable', 'favorites');
    }

    public function generateCode(){

        $this->timestamps=false;
        $this->code=rand(1000,9999);
        $this->expire_at=now()->addMinutes(10);
        $this->save();
    }
    public function isCodeValid() {
        return $this->expire_at < now();
    }

    public function resetCode(){
        $this->timestamps=false;
        $this->code=null;
        $this->expire_at=null;
        $this->save();
    }
}
