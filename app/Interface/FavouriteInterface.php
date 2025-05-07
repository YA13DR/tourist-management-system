<?php

namespace App\Interface;

interface FavouriteInterface
{
    public function showFavourite($id);
    public function showAllFavourite();
    public function addRestaurantToFavourite($id);
    public function addHotelToFavourite($id);
    public function addTourToFavourite($id);
    public function addPackageToFavourite($id);
}
