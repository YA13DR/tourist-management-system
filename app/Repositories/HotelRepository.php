<?php
namespace App\Repositories;

use App\Interface\HotelInterface;
use App\Http\Requests\HotelBookingRequest;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\Promotion;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Traits\ApiResponse;
use App\Traits\HandlesUserPoints;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class HotelRepository implements HotelInterface
{
    use ApiResponse,HandlesUserPoints;

    public function showHotel($id){
        $hotel = Hotel::with('images','roomTypes')
                    ->where('id',$id)
                        ->first();
       if (!$hotel) {
             return $this->error('Hotel not found', 404);
      }
      $user = auth()->user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $hotel->id,
                    'favoritable_type' => Hotel::class,
                ])->exists();
            }
            foreach ($hotel->roomTypes as $room) {
                $roomsData = [
                    'name ' => $room->name,
                    'number ' => $room->number,
                ];
            }
      return $this->success('Store retrieved successfully', [
        'hotel ' => $hotel,
        'image'=>$hotel->images,
        'rooms'=>$roomsData,
        'is_favourited' => $isFavourited,
    ]);
    }

    public function showAllHotel(){
        $hotels = Hotel::with('images','roomTypes')->get();

        $result = $hotels->map(function($hotel) {
            $user = auth()->user();
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $hotel->id,
                    'favoritable_type' => Hotel::class,
                ])->exists();
            }
            return [
                'hotel' => $hotel,
                'is_favourited' => $isFavourited,
            ];
        });
        return $this->success('All hotels retrieved successfully', [
            'hotels' => $result,
        ]);
    }

    public function showNearByHotel(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $radius = 5;

        $hotels = Hotel::selectRaw("*,
            (6371 * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )) AS distance", [$latitude, $longitude, $latitude])
        ->having("distance", "<=", $radius)
        ->orderBy("distance")
        ->get();

        return $this->success('Nearby Hotels retrieved successfully', [
            'hotels' => $hotels,
        ]);
    }
    public function showAviableRoom($id){
        $hotel=Hotel::with('roomTypes')->where('id',$id)->first();
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $hotelRoom=0;
        foreach($hotel->roomTypes as $roomType){
            $hotelRoom+=$roomType->number;
        }
        $bookingRoom = HotelBooking::where('hotel_id', $hotel->id)
        ->whereDate('checkInDate', '<=', now()->toDateString())
        ->whereDate('checkOutDate', '>', now()->toDateString())
        ->count();
        
        if($bookingRoom < $hotelRoom){
            $aviableRoom=$hotelRoom-$bookingRoom;
        }else{
            $aviableRoom=0;
        }

        return $this->success('Nearby Hotels retrieved successfully', [
            'aviableRoom' => $aviableRoom,
        ]);
    }
    public function showAviableRoomType($id,Request $request){
        $hotel=Hotel::with('roomTypes')->where('id',$id)->first();
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $hotelRoom=RoomType::where([
            'name'=>$request->roomType,
            'hotel_id'=>$hotel->id
            ])->first();
        if (!$hotelRoom) {
            return $this->error('Room type not found', 404);
        }
        $totalRooms = $hotelRoom->number;
        $bookingRoom=HotelBooking::where([
            'checkInDate'=>now()->toDateString(),
            'roomType_id'=>$hotelRoom->id
            ])->count();
        
        if($bookingRoom < $totalRooms){
            $aviableRoom=$totalRooms-$bookingRoom;
        }else{
            $aviableRoom=0;
        }

        return $this->success('Nearby Hotels retrieved successfully', [
            'aviableRoom' => $aviableRoom,
        ]);
    }

    public function bookHotel($id,HotelBookingRequest $request){
        $hotel=Hotel::with('roomTypes')->where('id',$id)->first();
        if (!$hotel) {
            return $this->error('hotel not found', 404);
        }
        $hotelRoom=RoomType::where([
            'id'=>$request->roomType_id,
            'hotel_id'=>$hotel->id
            ])->first();
        if (!$hotelRoom) {
            return $this->error('hotelRoom not found', 404);
        }
        $discount = $hotel->discount ?? 0;
        $checkInDate = Carbon::parse($request->checkInDate);
        $checkOutDate = $checkInDate->copy()->addDays($request->numberOfDays);

        $bookingRoom=HotelBooking::where('roomType_id',$hotelRoom->roomType_id)
        ->where('hotel_id',$hotel->id)
            ->whereDate('checkInDate', '<=', now()->toDateString())
            ->whereDate('checkOutDate', '>', now()->toDateString())
            ->count();
        if($bookingRoom > $hotelRoom->number){
            return $this->error('no aviable room', 404);
        }   
        $bookedRoomNumbers = HotelBooking::where('roomType_id', $hotelRoom->id)
        ->where('hotel_id', $hotel->id)
        ->where(function ($query) use ($checkInDate, $checkOutDate) {
            $query->whereBetween('checkInDate', [$checkInDate, $checkOutDate])
                ->orWhereBetween('checkOutDate', [$checkInDate, $checkOutDate])
                ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                    $q->where('checkInDate', '<=', $checkInDate)
                        ->where('checkOutDate', '>=', $checkOutDate);
                });
        })
        ->pluck('hotelRoom') 
        ->toArray();

        $allRoomNumbers = range(1, $hotelRoom->number);

        $availableRooms = array_diff($allRoomNumbers, $bookedRoomNumbers);

        if (empty($availableRooms)) {
        return $this->error('No available rooms for selected type and date', 400);
        }

        $assignedRoomNumber = collect($availableRooms)->random();

        $bookingReference = 'HB-' . strtoupper(uniqid());
        $totalCost = $hotelRoom->basePrice * $request->numberOfRooms * $request->numberOfDays;

        $discountAmount = ($discount > 0) ? ($totalCost * $discount / 100) : 0;
        $totalCostAfterDiscount = $totalCost - $discountAmount;

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 2, 
            'totalPrice' => $totalCostAfterDiscount, 
        'discountAmount' => $discountAmount,
            'paymentStatus' => 1,
        ]);
        $RoomReservation = HotelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'hotel_id' => $hotel->id,
            'roomType_id' => $request->roomType_id,
            'hotelRoom'=>$assignedRoomNumber ,
            'checkInDate' => $checkInDate,
            'checkOutDate' => $checkOutDate,
            'numberOfGuests' => $request->numberOfGuests,
            'numberOfRooms' => $request->numberOfRooms,
            'booking_id' => $booking->id,
            'cost' => $totalCostAfterDiscount,
        ]);
        $this->addPointsFromAction(auth('sanctum')->user(), 'book_hotel', 1);
        return $this->success('Nearby Hotels retrieved successfully', [
            'bookingReference' => $booking->bookingReference,
            'hotel' => $RoomReservation->hotel_id,
            'hotelRoom' => $RoomReservation->hotelRoom,
            'roomType' => $RoomReservation->roomType_id,
            'checkInDate' => $RoomReservation->checkInDate,
            'numberOfGuests' => $RoomReservation->numberOfGuests,
            'numberOfRoom' => $RoomReservation->numberOfRoom,
            'cost' => $RoomReservation->cost,
        'discountAmount' => $discountAmount,
        ]);
    }
    public function bookHotelWithPromotion($id, HotelBookingRequest $request)
    {
        $hotel = Hotel::with('roomTypes')->find($id);
        if (!$hotel) return $this->error('Hotel not found', 404);
    
        $hotelRoom = RoomType::where([
            'id' => $request->roomType_id,
            'hotel_id' => $hotel->id
        ])->first();
        if (!$hotelRoom) return $this->error('Hotel room not found', 404);
    
        $checkInDate = Carbon::parse($request->checkInDate);
        $checkOutDate = $checkInDate->copy()->addDays($request->numberOfDays);
    
        $bookedRoomNumbers = HotelBooking::where('roomType_id', $hotelRoom->id)
            ->where('hotel_id', $hotel->id)
            ->where(function ($query) use ($checkInDate, $checkOutDate) {
                $query->whereBetween('checkInDate', [$checkInDate, $checkOutDate])
                    ->orWhereBetween('checkOutDate', [$checkInDate, $checkOutDate])
                    ->orWhere(function ($q) use ($checkInDate, $checkOutDate) {
                        $q->where('checkInDate', '<=', $checkInDate)
                            ->where('checkOutDate', '>=', $checkOutDate);
                    });
            })
            ->pluck('hotelRoom')
            ->toArray();
    
        $allRoomNumbers = range(1, $hotelRoom->number);
        $availableRooms = array_diff($allRoomNumbers, $bookedRoomNumbers);
    
        if (empty($availableRooms)) {
            return $this->error('No available rooms for selected type and date', 400);
        }
    
        $assignedRoomNumber = collect($availableRooms)->random();
        $bookingReference = 'HB-' . strtoupper(uniqid());
    
        $totalCost = $hotelRoom->basePrice * $request->numberOfRooms * $request->numberOfDays;
    
        $promotion = null;
        $promotionCode = $request->promotion_code;
    
        if ($promotionCode) {
            $promotion = Promotion::where('promotion_code', $promotionCode)
            ->where('isActive', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->where('applicable_type', 1) 
                  ->orWhere('applicable_type', 3); 
            })
            ->first();

            if (!$promotion || !$promotion->isValid) {
                return $this->error('Invalid or expired promotion code', 400);
            }
    
            if ($totalCost < $promotion->minimum_purchase) {
                return $this->error('Total does not meet minimum purchase requirement for this code', 400);
            }
    
            if (!in_array($promotion->applicable_type, [null, 1, 3])) {
                return $this->error('This code cannot be applied to hotel bookings', 400);
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
            'discountAmount' => $discountAmount,
            'paymentStatus' => 1,
        ]);
    
        $RoomReservation = HotelBooking::create([
            'user_id' => auth('sanctum')->id(),
            'hotel_id' => $hotel->id,
            'roomType_id' => $request->roomType_id,
            'hotelRoom' => $assignedRoomNumber,
            'checkInDate' => $checkInDate,
            'checkOutDate' => $checkOutDate,
            'numberOfGuests' => $request->numberOfGuests,
            'numberOfRooms' => $request->numberOfRooms,
            'booking_id' => $booking->id,
            'cost' => $totalCostAfterDiscount,
        ]);
    
        if ($promotion) {
            $promotion->increment('current_usage');
        }
    
        $this->addPointsFromAction(auth('sanctum')->user(), 'book_hotel', 1);
    
        return $this->success('Hotel booked successfully with promotion', [
            'bookingReference' => $booking->bookingReference,
            'hotel' => $RoomReservation->hotel_id,
            'hotelRoom' => $RoomReservation->hotelRoom,
            'roomType' => $RoomReservation->roomType_id,
            'checkInDate' => $RoomReservation->checkInDate,
            'numberOfGuests' => $RoomReservation->numberOfGuests,
            'numberOfRoom' => $RoomReservation->numberOfRooms,
            'cost' => $RoomReservation->cost,
            'discountAmount' => $discountAmount,
            'promotion_code' => $promotion?->promotion_code,
        ]);
    }
    public function showHistory(){
        $user_id=auth('sanctum')->id();
        $hotelReservations = HotelBooking::with('hotel','roomType')->where('user_id',$user_id)->get();
        $result=[];
        foreach($hotelReservations as $hotelReservation){
            $result[]=[
                'hotel'=>$hotelReservation->hotel->name,
                'roomType'=>$hotelReservation->roomType->name,
                'numberOfGuests'=>$hotelReservation->numberOfGuests,
                'numberOfRoom'=>$hotelReservation->numberOfRoom,
                'checkInDate'=>$hotelReservation->checkInDate,
                'cost'=>$hotelReservation->cost,
            ];
        }

        return $this->success('Order added successfully', [
            'reservation_id' => $result,
        ]);
    }
    public function showReservationRoom(){
        $user_id=auth('sanctum')->id();
        $hotelReservations = HotelBooking::with('hotel', 'roomType')
        ->where('user_id', $user_id)
        ->where('checkInDate', '>', now())
        ->get();
        $result=[];
        foreach($hotelReservations as $hotelReservation){
            $result[]=[
                'hotel'=>$hotelReservation->hotel->name,
                'roomType'=>$hotelReservation->roomType->name,
                'numberOfGuests'=>$hotelReservation->numberOfGuests,
                'numberOfRoom'=>$hotelReservation->numberOfRoom,
                'checkInDate'=>$hotelReservation->checkInDate,
                'cost'=>$hotelReservation->cost,
            ];
        }

        return $this->success('Order added successfully', [
            'reservation_id' => $result,
        ]);
    }
}