<?php

namespace App\Interface;

use App\Http\Requests\HotelBookingRequest;
use Illuminate\Http\Request;


interface HotelInterface
{
    public function showHotel($id);
    public function showAllHotel();
    public function showNearByHotel(Request $request);
    public function showAviableRoom($id);
    public function showAviableRoomType($id,Request $request);
    public function bookHotel($id,HotelBookingRequest $request);
    public function showHistory();
}
