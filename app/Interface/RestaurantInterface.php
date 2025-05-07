<?php

namespace App\Interface;

use App\Http\Requests\RestaurantBookingRequest;
use App\Http\Requests\RestaurantOrderRequest;
use Illuminate\Http\Request;

interface RestaurantInterface
{
    public function showRestaurant($id);
    public function showAllRestaurant();
    public function showNearByRestaurant(Request $request);
    public function showMenuCategory();
    public function showMenuItem($id);
    public function bookTable($id,RestaurantBookingRequest $request);
    public function addOrder($id,RestaurantOrderRequest $request);
    public function showAviableTable($id);
}
