<?php
namespace App\Repositories;

use App\Interface\BookingInterface;
use App\Models\Booking;
use App\Models\HotelBooking;
use App\Models\RestaurantBooking;
use App\Models\TourBooking;
use App\Models\TravelBooking;
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
        ->get();
        $response = [
            'tour' => [],
            'hotel' => [],
            'restaurant' => [],
            'flight' => [],
            'taxi' => [],
        ];
        foreach ($bookings as $booking) {
            switch ($booking->booking_type) {
                case 'tour':
                    $response['tour'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'number_of_adults' => $booking->tourBooking->number_of_adults ?? 0,
                        'number_of_children' => $booking->tourBooking->number_of_children ?? 0,
                    ];
                    break;
                case 'hotel':
                    $response['hotel'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'hotel' => optional(optional(optional($booking->hotelBooking)->roomType)->hotel)?->only(['id', 'name', 'location']),
                        'room_type' => optional(optional($booking->hotelBooking)->roomType)->name ?? null,
                        'check_in_date' => optional($booking->hotelBooking)->check_in_date ?? null,
                        'check_out_date' => optional($booking->hotelBooking)->check_out_date ?? null,
                    ];
                    break;
                case 'package':
                    $response['flight'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'flight' => optional($booking->travelBooking->flight),
                        'number_of_people' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 'restaurant':
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }

                    $response['restaurant'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservation_date' => $booking->restaurantBooking->reservation_date ?? null,
                        'reservation_time' => $booking->restaurantBooking->reservation_time ?? null,
                        'guests' => $booking->restaurantBooking->number_of_guests ?? 0,
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
        // dd(Booking::where('user_id', $user->id)->get());
        
        $bookings = Booking::with([
            'tourBooking.tour',
            'tourBooking.schedule',
            'hotelBooking.roomType.hotel',
            'restaurantBooking.restaurant',
            'travelBooking.flight',
        ])
        ->where('user_id', $user->id)
        ->get();
    
        $response = [
            'tour' => [],
            'hotel' => [],
            'restaurant' => [],
            'flight' => [],
            'taxi' => [],
        ];
    
        foreach ($bookings as $booking) {
            switch ($booking->booking_type) {
                case 'tour':
                    $response['tour'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'tour' => optional($booking->tourBooking->tour)->only(['id', 'name', 'location']),
                        'schedule' => optional($booking->tourBooking->schedule),
                        'number_of_adults' => $booking->tourBooking->number_of_adults ?? 0,
                        'number_of_children' => $booking->tourBooking->number_of_children ?? 0,
                    ];
                    break;
                case 'hotel':
                    $response['hotel'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'hotel' => optional(optional(optional($booking->hotelBooking)->roomType)->hotel)?->only(['id', 'name', 'location']),
                        'room_type' => optional(optional($booking->hotelBooking)->roomType)->name ?? null,
                        'check_in_date' => optional($booking->hotelBooking)->check_in_date ?? null,
                        'check_out_date' => optional($booking->hotelBooking)->check_out_date ?? null,
                    ];
                    break;
                case 'package':
                    $response['flight'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'flight' => optional($booking->travelBooking->flight),
                        'number_of_people' => $booking->travelBooking->number_of_people ?? 0,
                    ];
                    break;
                case 'restaurant':
                    $orderItems = [];
                    if (!empty($booking->restaurantBooking->order)) {
                        $orderItems = json_decode($booking->restaurantBooking->order, true);
                    }

                    $response['restaurant'][] = [
                        'booking_reference' => $booking->booking_reference,
                        'date' => $booking->booking_date,
                        'total_price' => $booking->total_price,
                        'restaurant' => optional($booking->restaurantBooking->restaurant)?->only(['id', 'name', 'location']),
                        'reservation_date' => $booking->restaurantBooking->reservation_date ?? null,
                        'reservation_time' => $booking->restaurantBooking->reservation_time ?? null,
                        'guests' => $booking->restaurantBooking->number_of_guests ?? 0,
                        'order' => $orderItems,
                    ];
                    break;
            }
        }
    
        return $this->success('All bookings retrieved successfully', $response);
    }
    public function cancelBooking($id){
        $booking = Booking::find($id);
        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        if ($booking->payment_status == 'paid') {
            return $this->error('Cannot cancel booking because it is already paid', 400);
        }

        $createdAt = $booking->created_at;
        $now = now();

        if ($now->diffInHours($createdAt) > 24) {
            return $this->error('Cannot cancel booking after 24 hours of creation', 400);
        }

        $modelsMap = [
            'tour' => TourBooking::class,
            'hotel' => HotelBooking::class,
            'restaurant' => RestaurantBooking::class,
            'package' => TravelBooking::class,
        ];

        $type = $booking->booking_type;

        if (!isset($modelsMap[$type])) {
            return $this->error('Invalid booking type', 400);
        }

        $detailModel = $modelsMap[$type];

        $detailRecord = $detailModel::where('booking_id', $id)->first();
        if ($detailRecord) {
            $detailRecord->delete();
        }

        $booking->delete();

        return $this->success('Booking cancelled successfully');
    }

}