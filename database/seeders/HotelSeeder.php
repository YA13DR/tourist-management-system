<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelAmenity;
use App\Models\HotelAmenityMap;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotel = Hotel::create([
            'name' => 'Grand Palace Hotel',
            'description' => 'A luxurious hotel in the heart of the city.',
            'discount' => 10,
            'starRating' => 5,
            'checkInTime' => '14:00:00',
            'checkOutTime' => '12:00:00',
            'averageRating' => 4.7,
            'totalRatings' => 124,
            'mainImageURL' => 'images/hotels/grand-palace.jpg',
            'website' => 'https://grandpalace.com',
            'phone' => '+1 234 567 8900',
            'email' => 'contact@grandpalace.com',
            'isActive' => true,
            'isFeatured' => true,
            'admin_id' => 5,
        ]);

        $roomType = RoomType::create([
            'hotel_id' => $hotel->id,
            'name' => 'Deluxe King Room',
            'number' => 20,
            'description' => 'Spacious room with king-sized bed.',
            'maxOccupancy' => 2,
            'basePrice' => 200.00,
            'discountPercentage' => 15.00,
            'size' => '35 sqm',
            'bedType' => 'King',
            'imageURL' => 'images/rooms/deluxe-king.jpg',
            'isActive' => true,
        ]);

        foreach (range(0, 6) as $offset) {
            RoomAvailability::create([
                'roomType_id' => $roomType->id,
                'date' => now()->addDays($offset)->toDateString(),
                'availableRooms' => 10,
                'price' => 180.00,
                'isBlocked' => false,
            ]);
        }
        $amenities = [
            ['name' => 'Free Wi-Fi', 'iconURL' => 'icons/wifi.png'],
            ['name' => 'Swimming Pool', 'iconURL' => 'icons/pool.png'],
            ['name' => 'Gym', 'iconURL' => 'icons/gym.png'],
            ['name' => 'Spa', 'iconURL' => 'icons/spa.png'],
            ['name' => 'Airport Shuttle', 'iconURL' => 'icons/shuttle.png'],
        ];

        foreach ($amenities as $item) {
            $amenity = HotelAmenity::create([
                'name' => $item['name'],
                'iconURL' => $item['iconURL'],
                'isActive' => true,
            ]);

            HotelAmenityMap::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenity->id,
            ]);
        }
    }
}
