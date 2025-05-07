<?php

namespace App\Repositories;

use App\Http\Requests\TourBookingRequest;
use App\Http\Requests\TravelBookingRequest;
use App\Interface\PackageInterface;
use App\Models\Booking;
use App\Models\PackageBooking;
use App\Models\TravelBooking;
use App\Models\TravelPackage;
use App\Models\Favourite;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use DB;

class PackageRepository implements PackageInterface
{
    use ApiResponse;

    public function showAllPackages()
    {
        $packages = TravelPackage::with([
            'destinations.location',
            'inclusions',
            'agency'
        ])->where('isActive', true)->get();

        $user = auth()->user();

        $result = $packages->map(function ($package) use ($user) {
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $package->id,
                    'favoritable_type' => TravelPackage::class,
                ])->exists();
            }

            return [
                'package' => $package,
                'is_favourited' => $isFavourited,
            ];
        });

        return $this->success('All packages retrieved successfully', [
            'packages' => $result,
        ]);
    }
    public function showAllPackagesAgency($id)
    {
        $packages = TravelPackage::with([
            'destinations.location',
            'inclusions',
            'agency'
        ])->where('agency_id',$id)->where('isActive', true)->get();

        $user = auth()->user();

        $result = $packages->map(function ($package) use ($user) {
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $package->id,
                    'favoritable_type' => TravelPackage::class,
                ])->exists();
            }

            return [
                'package' => $package,
                'is_favourited' => $isFavourited,
            ];
        });

        return $this->success('All packages retrieved successfully', [
            'packages' => $result,
        ]);
    }

    public function showPackage($id)
    {
        $package = TravelPackage::with([
            'destinations.location',
            'inclusions',
            'agency'
        ])->find($id);

        if (!$package) {
            return $this->error('Package not found', 404);
        }

        $user = auth()->user();
        $isFavourited = false;
        if ($user) {
            $isFavourited = Favourite::where([
                'user_id' => $user->id,
                'favoritable_id' => $package->id,
                'favoritable_type' => TravelPackage::class,
            ])->exists();
        }

        return $this->success('Package retrieved successfully', [
            'package' => $package,
            'is_favourited' => $isFavourited,
        ]);
    }
    public function bookTravelPackage($id, TravelBookingRequest $request)
    {
        $package = TravelPackage::find($id);

        if (!$package) {
            return $this->error('Package not found', 404);
        }

        $schedule = $package->where('isActive', true)->first();

        if (!$schedule) {
            return $this->error('No active schedule found for this package.', 404);
        }

        $now = Carbon::now();
        // $startDate = Carbon::parse($schedule->startDate);

        // if ($now->greaterThanOrEqualTo($startDate->subDay())) {
        //     return $this->error('Booking must be made at least one day before the package start date.', 400);
        // }

        $existingBookings = PackageBooking::where('package_id', $package->id)->sum(DB::raw('numberOfAdults + numberOfChildren'));

        $newBookingCount = $request->numberOfAdults + $request->numberOfChildren;
        $totalAfterBooking = $existingBookings + $newBookingCount;

        if ($totalAfterBooking > $package->maxParticipants) {
            return $this->error('Cannot book: capacity exceeded.', 400);
        }
        $bookingReference = 'PKG-' . strtoupper(uniqid());
        $totalCost = $package->basePrice * $newBookingCount;

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 5, 
            'totalPrice' => $totalCost,
            'paymentStatus' => 1, 
        ]);

        $packageBooking = PackageBooking::create([
            'booking_id' => $booking->id,
            'user_id' => auth('sanctum')->id(),
            'package_id' => $package->id,
            'agency_id' => $package->agency_id,
            'numberOfAdults' => $request->numberOfAdults,
            'numberOfChildren' => $request->numberOfChildren,
            'cost' => $totalCost,
        ]);
    
        return $this->success('Package booked successfully', [
            'bookingReference' => $booking->bookingReference,
            'packageBooking_id' => $packageBooking->id,
            'package_id' => $packageBooking->package_id,
            'total_cost' => $packageBooking->cost,
        ]);
    }
}
