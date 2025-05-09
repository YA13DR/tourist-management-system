<?php

namespace App\Repositories;

use App\Http\Requests\travelBookingRequest;
use App\Interface\PackageInterface;
use App\Interface\TravelInterface;
use App\Models\Booking;
use App\Models\PackageBooking;
use App\Models\TravelAgency;
use App\Models\TravelBooking;
use App\Models\TravelFlight;
use App\Models\TravelPackage;
use App\Models\Favourite;
use App\Traits\ApiResponse;
use Auth;
use Carbon\Carbon;
use DB;
use Request;

class TravelRepository implements TravelInterface
{
    use ApiResponse;

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

        $now = Carbon::now();

        if ($flight->departure_time <= $now) {
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
                'flight_id' => $flight->id,
                'number_of_people' => $request->number_of_people,
                'booking_date' => now()->toDateString(),
                'total_price' => $totalCost,
                'status' => 'confirmed',
                'booking_id' => $booking->id, 
            ]);

            return $this->success('Flight booked successfully', [
                'bookingReference' => $booking->bookingReference,
                'reservation_id' => $travelBooking->id,
                'flight_id' => $flight->id,
                'departure_time' => $flight->departure_time,
                'total_cost' => $totalCost,
            ]);
    }
}
