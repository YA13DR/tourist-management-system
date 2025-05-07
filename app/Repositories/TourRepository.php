<?php

namespace App\Repositories;

use App\Http\Requests\TourBookingRequest;
use App\Interface\TourInterface;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Tour;
use App\Models\TourBooking;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TourRepository implements TourInterface
{
    use ApiResponse;

    public function showAllTour(){
        $tours=Tour::with('images','schedules','admin')
        ->get()
        ->filter(fn($tour) => $tour->schedules->isNotEmpty()) 
        ->values(); 
    $result = $tours->map(function($tour) {
        $user = auth()->user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $tour->id,
                    'favoritable_type' => Tour::class,
                ])->exists();
            }
        return [
            'tour' => $tour,
            'is_favourited' => $isFavourited,
        ];
    });
        return $this->success('All tours retrieved successfully', [
            'tours' => $result,
        ]);
    }

    public function showTour($id){
        $tour = Tour::with('images', 'schedules')
                    ->where('id', $id)
                    ->first();
       if (!$tour || $tour->schedules->isEmpty()) {
             return $this->error('Tour not found', 404);
         }
         $user = auth()->user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $tour->id,
                    'favoritable_type' => Tour::class,
                ])->exists();
            }

        return $this->success('Store retrieved successfully', [
                'tour ' => $tour,
                'is_favourited' => $isFavourited,
        ]);
    }

    public function bookTour($id,TourBookingRequest $request){
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->error('tour not found', 404);
        }
        $schedule = $tour->schedules()->where('isActive', true)->first();

        if (!$schedule) {
            return $this->error('No active schedule found for this tour.', 404);
        }
        $now = Carbon::now();
        $startDate = Carbon::parse($schedule->startDate);
    
        if ($now->greaterThanOrEqualTo($startDate->subDay())) {
            return $this->error('Booking must be made at least one day before the tour start date.', 400);
        }
        $existingBookings = TourBooking::where('tour_id', $tour->id)->sum(DB::raw('numberOfAdults + numberOfChildren'));

        $newBookingCount = $request->numberOfAdults + $request->numberOfChildren;
        $totalAfterBooking = $existingBookings + $newBookingCount;
        if ($totalAfterBooking > $tour->maxCapacity) {
            return $this->error('Cannot book: capacity exceeded.', 400);
        }
        $bookingReference = 'TB-' . strtoupper(uniqid());
        $totalCost = $tour->basePrice * $newBookingCount;
    
        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 1, 
            'totalPrice' => $totalCost,
            'paymentStatus' => 1, 
        ]);
        $tourReservation = TourBooking::create([
            'user_id' => auth('sanctum')->id(),
            'tour_id' => $tour->id,
            'schedule_id' => $schedule->id,
            'numberOfAdults' => $request->numberOfAdults,
            'numberOfChildren' => $request->numberOfChildren,
            'booking_id' => $booking->id,
            'cost' =>$tour->basePrice * ($request->numberOfAdults+ $request->numberOfChildren), 
        ]);
    

        return $this->success('Table reserved successfully', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $tourReservation->cost,
        ]);
    }

}