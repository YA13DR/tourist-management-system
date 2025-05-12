<?php

namespace App\Repositories;

use App\Http\Requests\travelBookingRequest;
use App\Interface\PackageInterface;
use App\Interface\TravelInterface;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\PackageBooking;
use App\Models\Promotion;
use App\Models\Rank;
use App\Models\TravelAgency;
use App\Models\TravelBooking;
use App\Models\TravelFlight;
use App\Models\TravelPackage;
use App\Models\Favourite;
use App\Models\UserRank;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Auth;
use Carbon\Carbon;
use DB;
use Request;

class TravelRepository implements TravelInterface
{
    use ApiResponse , HandlesUserPoints;

    public function getAllFlights()
    {
        $flights = TravelFlight::with(['agency', 'departure', 'arrival'])
            ->get()
            ->filter(fn($flight) => $flight->available_seats > 0)
            ->values();

            
            return $this->success('All flights retrieved successfully', [
                'flig$flights' => $flights,
            ]);
    }

    public function getFlight($id)
    {
        $flight = TravelFlight::with(['agency', 'departure', 'arrival'])->find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        return $this->success('All flig$flight retrieved successfully', [
                'flig$flight' => $flight,
        ]);
    }

    public function getAvailableFlights()
    {
        $now = now();
    
        $flights = TravelFlight::where('departure_time', '>=', $now)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->with(['agency', 'departure', 'arrival'])
            ->get();
    
        return $this->success('Upcoming available flights retrieved', $flights);
    }
    public function getAvailableFlightsDate(Request $request)
    {
        $request->validate([
            'time' => 'required|date',
        ]);
    
        $time = Carbon::parse($request->time);
    
        $flights = TravelFlight::where('departure_time', '>=', $time)
            ->where('available_seats', '>', 0)
            ->orderBy('departure_time', 'asc')
            ->with(['agency', 'departure', 'arrival'])
            ->get();
    
        return $this->success('Available flights from selected time retrieved', $flights);
    }
    public function getAgency($id)
    {
        $agency = TravelAgency::where('id', $id)
            ->with(['location', 'flights','admin'])
            ->get();

        return $this->success('agency by agency retrieved', $agency);
    }
    public function getAllAgency()
    {
        $agency = TravelAgency::with(['location', 'flights','admin'])
            ->get();

        return $this->success('agency by agency retrieved', $agency);
    }


    public function bookFlight($id, TravelBookingRequest $request)
    {
        $flight = TravelFlight::find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $alreadyBookedSeats = TravelBooking::where('flight_id', $flight->id)
            ->where('status', '!=', 'cancelled')
            ->sum('number_of_people');

        $remainingSeats = $flight->available_seats - $alreadyBookedSeats;

        if ($request->number_of_people > $remainingSeats) {
            return $this->error('Not enough available seats. Only ' . $remainingSeats . ' remaining.', 400);
        }

        $bookingReference = 'FB-' . strtoupper(uniqid());
        $totalCost = $flight->price * $request->number_of_people;

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 2,
            'totalPrice' => $totalCost,
            'paymentStatus' => 1,
        ]);

        $travelBooking = TravelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'number_of_people' => $request->number_of_people,
            'booking_date' => now()->toDateString(),
            'total_price' => $totalCost,
            'status' => 'confirmed',
        ]);

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_flight', $request->number_of_people);

        return $this->success('Flight booked successfully', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $travelBooking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'total_cost' => $totalCost,
        ]);
    }

    public function bookFlightByPoint($id, TravelBookingRequest $request){
        $flight = TravelFlight::find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $alreadyBookedSeats = TravelBooking::where('flight_id', $flight->id)
            ->where('status', '!=', 'cancelled')
            ->sum('number_of_people');

        $remainingSeats = $flight->available_seats - $alreadyBookedSeats;

        if ($request->number_of_people > $remainingSeats) {
            return $this->error('Not enough available seats. Only ' . $remainingSeats . ' remaining.', 400);
        }

        $user = auth('sanctum')->user();
        $userRank = $user->rank ?? new UserRank(['user_id' => $user->id]);
        $userPoints = $userRank->points_earned ?? 0;

        $rule = DiscountPoint::where('action', 'book_flight')->first();

        if (!$rule || $userPoints < $rule->required_points) {
            return $this->error('You do not have enough reward points to book this flight. Minimum required: ' . ($rule->required_points ?? 'N/A'), 403);
        }

        $discount = ($flight->price * $request->number_of_people) * ($rule->discount_percentage / 100);
        $totalCost = ($flight->price * $request->number_of_people) - $discount;

        $booking = Booking::create([
            'bookingReference' => 'FB-' . strtoupper(uniqid()),
            'user_id' => $user->id,
            'bookingType' => 2,
            'totalPrice' => $totalCost,
            'paymentStatus' => 1,
        ]);

        $travelBooking = TravelBooking::create([
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'number_of_people' => $request->number_of_people,
            'booking_date' => now()->toDateString(),
            'total_price' => $totalCost,
            'discountAmount' => $discount,
            'paymentStatus' => 1,
            'bookingDate' => now(), 
            'status' => 'confirmed',
        ]);

        $userRank->points_earned -= $rule->required_points;
        $userRank->save();

        return $this->success('Flight booked successfully with discount applied.', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $travelBooking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'total_cost' => $totalCost,
            'discount_applied' => true,
            'discount_amount' => $discount,
        ]);
    }

    public function bookFlightWithPromotion($id, TravelBookingRequest $request){
        $flight = TravelFlight::find($id);

        if (!$flight) {
            return $this->error('Flight not found', 404);
        }

        if ($flight->departure_time <= now()) {
            return $this->error('Cannot book a flight that has already departed.', 400);
        }

        $alreadyBookedSeats = TravelBooking::where('flight_id', $flight->id)
            ->where('status', '!=', 'cancelled')
            ->sum('number_of_people');

        $remainingSeats = $flight->available_seats - $alreadyBookedSeats;

        if ($request->number_of_people > $remainingSeats) {
            return $this->error('Not enough available seats. Only ' . $remainingSeats . ' remaining.', 400);
        }

        $bookingReference = 'FB-' . strtoupper(uniqid());
        $totalCost = $flight->price * $request->number_of_people;

        $promotion = null;
        $promotionCode = $request->promotion_code;

        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
                ->where('isActive', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where(function ($q) {
                    $q->where('applicable_type', 1) 
                    ->orWhere('applicable_type', 7); 
                })
                ->first();

            if (!$promotion || !$promotion->isActive) {
                return $this->error('Invalid or expired promotion code', 400);
            }

            if ($totalCost < $promotion->minimum_purchase) {
                return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
            }

            if (!in_array($promotion->applicable_type, [null, 1, 7])) {
                return $this->error('This code cannot be applied to this flight booking', 400);
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
            'bookingType' => 2,
            'totalPrice' => $totalCostAfterDiscount,
            'paymentStatus' => 1,
        ]);

        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }

        $travelBooking = TravelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'booking_id' => $booking->id,
            'flight_id' => $flight->id,
            'number_of_people' => $request->number_of_people,
            'booking_date' => now()->toDateString(),
            'total_price' => $totalCostAfterDiscount,
            'status' => 'confirmed',
        ]);

        if ($promotion) {
            $promotion->increment('current_usage');
        }

        $this->addPointsFromAction(auth('sanctum')->user(), 'book_flight', $request->number_of_people);

        return $this->success('Flight booked successfully', [
            'bookingReference' => $booking->bookingReference,
            'reservation_id' => $travelBooking->id,
            'flight_id' => $flight->id,
            'departure_time' => $flight->departure_time,
            'total_cost' => $totalCostAfterDiscount,
            'discountAmount' => $discountAmount,
        ]);
    }
}
