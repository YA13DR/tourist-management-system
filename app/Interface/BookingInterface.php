<?php

namespace App\Interface;

use App\Http\Requests\PayRequest;
use Illuminate\Support\Facades\Request;

interface BookingInterface
{
    
    public function payForBooking($id , PayRequest $request);
    public function getBookingHistory();
    public function getAllBookings();
    public function cancelBooking($id);
     public function modifyBooking(Request $request, $id);
}
