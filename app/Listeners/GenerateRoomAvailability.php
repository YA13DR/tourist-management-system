<?php

namespace App\Listeners;

use App\Events\HotelRoom;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RoomAvailability;
use Illuminate\Support\Carbon;


class GenerateRoomAvailability
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(HotelRoom $event): void
    {
        $roomType = $event->roomType;

        $data = [];

        for ($i = 0; $i <= 180; $i++) {
            $date = Carbon::now()->addDays($i)->toDateString();

            $data[] = [
                'roomType_id' => $roomType->id,
                'Date' => $date,
                'AvailableRooms' => $roomType->number,
                'Price' => $roomType->basePrice,
                'IsBlocked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        RoomAvailability::insert($data);
    }
}