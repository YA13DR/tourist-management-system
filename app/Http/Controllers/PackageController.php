<?php

namespace App\Http\Controllers;

use App\Http\Requests\TravelBookingRequest;
use App\Interface\PackageInterface;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    protected $packageRepository;
    public function __construct(PackageInterface $packageRepository)
    {
        $this->packageRepository = $packageRepository;
    }
    public function showpackage($id){
        return $this->packageRepository->showpackage($id);
    }
    public function showAllPackages(){
        return $this->packageRepository->showAllPackages();
    }
    public function showAllAgency(){
        return $this->packageRepository->showAllAgency();
    }
    public function showAllPackagesAgency($id){
        return $this->packageRepository->showAllPackagesAgency($id);
    }
    public function bookTravelPackage($id,TravelBookingRequest $request){
        return $this->packageRepository->bookTravelPackage($id,$request);
    }
}
