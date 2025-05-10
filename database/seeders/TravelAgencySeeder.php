<?php

namespace Database\Seeders;

use App\Models\PackageDestination;
use App\Models\PackageInclusion;
use App\Models\TravelAgency;
use App\Models\TravelFlight;
use App\Models\TravelPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agency =TravelAgency::create([
            'name' => 'Global Explorer Travel Agency',
            'description' => 'We provide international travel and tourism services.',
            'location_id' => 1,
            'website' => 'https://globalexplorer.com',
            'phone' => '+1 555-123-4567',
            'email' => 'contact@globalexplorer.com',
            'isActive' => true,
            'admin_id' => 9,
        ]);

        TravelFlight::create([
            'agency_id' => $agency->id,
            'flight_number' => 'GE' . strtoupper(uniqid()),
            'departure_id' => 1, 
            'arrival_id' => 1,   
            'departure_time' => now()->addDays(3)->setTime(10, 30),
            'arrival_time' => now()->addDays(3)->setTime(14, 15),
            'duration_minutes' => 225,
            'price' => 350.00,
            'available_seats' => 120,
            'status' => 'scheduled',
        ]);
        $package=TravelPackage::create([
            'agency_id' => $agency->id,
            'name' => 'Amazing Europe Tour',
            'description' => '10-day guided tour across France, Italy, and Switzerland.',
            'durationDays' => 10,
            'basePrice' => 2500.00,
            'discountPercentage' => 10,
            'maxParticipants' => 30,
            'averageRating' => 4.7,
            'totalRatings' => 134,
            'mainImageURL' => 'https://example.com/images/europe-tour.jpg',
            'isActive' => true,
            'isFeatured' => true,
        ]);
        PackageDestination::create([
            'package_id' => $package->id,
            'location_id' => 1, 
            'dayNumber' => 3,
            'description' => 'Visit Colosseum and Vatican.',
            'duration' => '2 days',
        ]);
        PackageInclusion::create([
            'package_id' => $package->id,
            'inclusionType' => 1,
            'description' => '4-star hotel with breakfast included',
            'isHighlighted' => true,
        ]);

        PackageInclusion::create([
            'package_id' => $package->id,
            'inclusionType' => 2,
            'description' => 'Airport pickup and inter-city travel',
            'isHighlighted' => false,
        ]);
    }
}
