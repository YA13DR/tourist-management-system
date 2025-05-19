<?php

namespace App\Interface;

interface BookingInterface
{
    public function getBookingHistory();
    public function getAllBookings();
    public function cancelBooking($id);
}
