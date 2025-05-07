<?php

namespace App\Interface;

use App\Http\Requests\TourBookingRequest;

interface TourInterface
{
    public function showAllTour();
    public function showTour($id);
    public function bookTour($id,TourBookingRequest $request);
}
