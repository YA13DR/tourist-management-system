<?php

namespace App\Repositories;

use App\Http\Requests\TourBookingRequest;
use App\Interface\TourInterface;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\Favourite;
use App\Models\Promotion;
use App\Models\Tour;
use App\Models\TourBooking;
use App\Models\UserRank;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TourRepository implements TourInterface
{
    use ApiResponse , HandlesUserPoints;

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
            $now = now();
        $promotion = Promotion::where('isActive', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('applicable_type', 1) 
            ->orwhere('applicable_type', 2) 
            ->first();
        return [
            'tour' => $tour,
            'is_favourited' => $isFavourited,
            'promotion' => $promotion ? [
                'promotion_code' => $promotion->promotion_code,
                'description' => $promotion->description,
                'discount_type' => $promotion->discount_type,
                'discount_value' => $promotion->discount_value,
                'minimum_purchase' => $promotion->minimum_purchase,
            ] : null,
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
            $now = now();
            $promotion = Promotion::where('isActive', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 1) 
                ->orwhere('applicable_type', 2) 
                ->first();
        return $this->success('Store retrieved successfully', [
                'tour ' => $tour,
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
        ]);
    }

    public function bookTourWithPromo($id,TourBookingRequest $request){
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
        $this->addPointsFromAction(auth('sanctum')->user(), 'book_tour', $newBookingCount);


        return $this->success('Table reserved successfully', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $tourReservation->cost,
        ]);
    }
    public function bookTourByPoint($id,TourBookingRequest $request){
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->error('Tour not found', 404);
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

        $existingBookings = TourBooking::where('tour_id', $tour->id)
            ->sum(DB::raw('numberOfAdults + numberOfChildren'));

        $newBookingCount = $request->numberOfAdults + $request->numberOfChildren;
        $totalAfterBooking = $existingBookings + $newBookingCount;

        if ($totalAfterBooking > $tour->maxCapacity) {
            return $this->error('Cannot book: capacity exceeded.', 400);
        }

        $user = auth('sanctum')->user();
        $userRank = $user->rank ?? new UserRank(['user_id' => $user->id]);
        $userPoints = $userRank->points_earned ?? 0;

        $rule = DiscountPoint::where('action', 'book_tour')->first();

        if (!$rule || $userPoints < $rule->required_points) {
            return $this->error('You do not have enough reward points to book this tour. Minimum required: ' . ($rule->required_points ?? 'N/A'), 403);
        }

        $originalCost = $tour->basePrice * $newBookingCount;
        $discountAmount = $originalCost * ($rule->discount_percentage / 100);
        $totalCost = $originalCost - $discountAmount;

        $bookingReference = 'TB-' . strtoupper(uniqid());
        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => $user->id,
            'bookingType' => 1,
            'totalPrice' => $totalCost,
            'discountAmount' => $discountAmount,
            'paymentStatus' => 1,
            'bookingDate' => now(), 
            'status' => 'confirmed',
        ]);

        $tourReservation = TourBooking::create([
            'user_id' => $user->id,
            'tour_id' => $tour->id,
            'schedule_id' => $schedule->id,
            'numberOfAdults' => $request->numberOfAdults,
            'numberOfChildren' => $request->numberOfChildren,
            'booking_id' => $booking->id,
            'cost' => $totalCost,
        ]);

        $userRank->points_earned -= $rule->required_points;
        $userRank->save();

        return $this->success('Tour booked successfully with discount applied.', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $totalCost,
            'discount_applied' => true,
            'discount_amount' => $discountAmount,
        ]);
    }

    public function bookTour($id, TourBookingRequest $request){
        $tour = Tour::find($id);

        if (!$tour) {
            return $this->error('Tour not found', 404);
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

        $promotion = null;
        $promotionCode = $request->promotion_code;

        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
                ->where('isActive', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 1) 
                    ->orWhere('applicable_type', 2); 
                })
                ->first();

            if (!$promotion || !$promotion->isActive) {
                return $this->error('Invalid or expired promotion code', 400);
            }

            if ($totalCost < $promotion->minimum_purchase) {
                return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
            }

            if (!in_array($promotion->applicable_type, [null, 1, 2])) {
                return $this->error('This code cannot be applied to this tour booking', 400);
            }
        }

        $discountAmount = 0;
        if ($promotion) {
            $discountAmount = $promotion->discount_type == 1
                ? ($totalCost * $promotion->discount_value / 100) 
                : $promotion->discount_value;

            $discountAmount = min($discountAmount, $totalCost);
        }

        $totalCostAfterDiscount = $totalCost - $discountAmount;

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 1,
            'totalPrice' => $totalCostAfterDiscount,
            'paymentStatus' => 1, 
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        $tourReservation = TourBooking::create([
            'user_id' => auth('sanctum')->id(),
            'tour_id' => $tour->id,
            'schedule_id' => $schedule->id,
            'numberOfAdults' => $request->numberOfAdults,
            'numberOfChildren' => $request->numberOfChildren,
            'booking_id' => $booking->id,
            'cost' => $totalCostAfterDiscount,
        ]);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_tour', $newBookingCount);

        return $this->success('Tour booked successfully', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $tourReservation->id,
            'tour' => $tourReservation->tour_id,
            'schedule' => $tourReservation->schedule_id,
            'cost' => $tourReservation->cost,
            'discountAmount' => $discountAmount,
        ]);
    }

}