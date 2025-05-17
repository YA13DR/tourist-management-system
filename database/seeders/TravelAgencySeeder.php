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
            'is_active' => true,
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
            'duration_days' => 10,
            'base_price' => 2500.00,
            'discount_percentage' => 10,
            'max_participants' => 30,
            'average_rating' => 4.7,
            'total_ratings' => 134,
            'main_image' => 'https://example.com/images/europe-tour.jpg',
            'is_active' => true,
            'is_featured' => true,
        ]);
        PackageDestination::create([
            'package_id' => $package->id,
            'location_id' => 1, 
            'day_number' => 3,
            'description' => 'Visit Colosseum and Vatican.',
            'duration' => '2 days',
        ]);
        PackageInclusion::create([
            'package_id' => $package->id,
            'inclusion_type' => 1,
            'description' => '4-star hotel with breakfast included',
            'is_highlighted' => true,
        ]);

        PackageInclusion::create([
            'package_id' => $package->id,
            'inclusion_type' => 2,
            'description' => 'Airport pickup and inter-city travel',
            'is_highlighted' => false,
        ]);
    }
}
