<?php
namespace App\Repositories;

use App\Interface\BookingInterface;
use App\Models\Booking;
use App\Traits\ApiResponse;

class BookingRepository implements BookingInterface
{
    use ApiResponse;
    public function getBookingHistory(){
        $user = auth('sanctum')->user();

        $bookings = Booking::with([
            'tourBooking.tour',
            'hotelBooking.hotel',
            'restaurantBooking.restaurant',
            'travelBooking.flight',
        ])
        ->where('user_id', $user->id)
        ->where('status', 4)
        ->latest()
        ->get();
        $response = [
            'tours' => [],
            'hotels' => [],
            'restaurants' => [],
            'flights' => [],
            'taxis' => [],
        ];
        foreach ($bookings as $booking) {
            switch ($booking->bookingType) {
                case 1:
                    $response['tours'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'numberOfAdults' => $booking->tourBooking->numberOfAdults ?? 0,
                        'numberOfChildren' => $booking->tourBooking->numberOfChildren ?? 0,
                    ];
                    break;
                case 2:
                    $response['hotels'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'hotel' => optional($booking->hotelBooking->roomType->hotel)->only(['id', 'name', 'location']),
                        'roomType' => optional($booking->hotelBooking->roomType)->name,
                        'checkIn' => $booking->hotelBooking->checkInDate ?? null,
                        'checkOut' => $booking->hotelBooking->checkOutDate ?? null,
                    ];
                    break;
                case 3:
                    $response['flights'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'flight' => optional($booking->travelBooking->flight),
                        'numberOfPeople' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 4:
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }

                    $response['restaurants'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservationDate' => $booking->restaurantBooking->reservationDate ?? null,
                        'reservationTime' => $booking->restaurantBooking->reservationTime ?? null,
                        'guests' => $booking->restaurantBooking->numberOfGuests ?? 0,
                        'order' => $orderItems,
                    ];
                    break;
            }
        }
    
        return $this->success('Booking history retrieved successfully', $response);
    }
    public function getAllBookings()
    {
        $user= auth('sanctum')->user();
    
        $bookings = Booking::with([
            'tourBooking.tour',
            'tourBooking.schedule',
            'hotelBooking.roomType.hotel',
            'restaurantBooking.restaurant',
            'travelBooking.flight',
        ])
        ->where('user_id', $user->id)
        ->latest()
        ->get();
    
        $response = [
            'tours' => [],
            'hotels' => [],
            'restaurants' => [],
            'flights' => [],
            'taxis' => [],
        ];
    
        foreach ($bookings as $booking) {
            switch ($booking->bookingType) {
                case 1:
                    $response['tours'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'status' => $booking->status,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'numberOfAdults' => $booking->tourBooking->numberOfAdults ?? 0,
                        'numberOfChildren' => $booking->tourBooking->numberOfChildren ?? 0,
                    ];
                    break;
                case 2:
                    $response['hotels'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'status' => $booking->status,
                        'hotel' => optional($booking->hotelBooking->roomType->hotel)->only(['id', 'name', 'location']),
                        'roomType' => optional($booking->hotelBooking->roomType)->name,
                        'checkIn' => $booking->hotelBooking->checkInDate ?? null,
                        'checkOut' => $booking->hotelBooking->checkOutDate ?? null,
                    ];
                    break;
                case 3:
                    $response['flights'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'status' => $booking->status,
                        'flight' => optional($booking->travelBooking->flight),
                        'numberOfPeople' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 4:
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }
                
                    $response['restaurants'][] = [
                        'bookingReference' => $booking->bookingReference,
                        'date' => $booking->bookingDate,
                        'totalPrice' => $booking->totalPrice,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservationDate' => $booking->restaurantBooking->reservationDate ?? null,
                        'reservationTime' => $booking->restaurantBooking->reservationTime ?? null,
                        'guests' => $booking->restaurantBooking->numberOfGuests ?? 0,
                        'order' => $orderItems, 
                    ];
                    break;
            }
        }
    
        return $this->success('All bookings retrieved successfully', $response);
    }
    

}