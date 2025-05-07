<?php
namespace App\Repositories;

use App\Interface\HotelInterface;
use App\Http\Requests\HotelBookingRequest;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\Hotel;
use App\Models\HotelBooking;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class HotelRepository implements HotelInterface
{
    use ApiResponse;

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

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 2, 
            'totalPrice' => $totalCost,
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
            'cost' => $hotelRoom->basePrice * $request->numberOfRooms * $request->numberOfDays,
        ]);

        return $this->success('Nearby Hotels retrieved successfully', [
            'bookingReference' => $booking->bookingReference,
            'hotel' => $RoomReservation->hotel_id,
            'hotelRoom' => $RoomReservation->hotelRoom,
            'roomType' => $RoomReservation->roomType_id,
            'checkInDate' => $RoomReservation->checkInDate,
            'numberOfGuests' => $RoomReservation->numberOfGuests,
            'numberOfRoom' => $RoomReservation->numberOfRoom,
            'cost' => $RoomReservation->cost,
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