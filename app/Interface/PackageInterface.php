<?php

namespace App\Interface;

use App\Http\Requests\TravelBookingRequest;

interface PackageInterface
{
    public function showAllPackages();
    public function showAllPackagesAgency($id);
    public function showPackage($id);
    public function showAllAgency();
    public function bookTravelPackage($id, TravelBookingRequest $request);
    
}
