<?php

namespace App\Http\Controllers;

use App\Interface\BookingInterface;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    protected $bookingRepository;
    public function __construct(BookingInterface $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }
    public function getBookingHistory(){
        return $this->bookingRepository->getBookingHistory();
    }
    public function getAllBookings(){
        return $this->bookingRepository->getAllBookings();
    }
}
