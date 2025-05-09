<?php

namespace App\Interface;

use App\Http\Requests\PackageBookingRequest;

interface PackageInterface
{
    public function showAllPackages();
    public function showPackage($id);
    public function bookTravelPackage($id, PackageBookingRequest $request);
    
}
