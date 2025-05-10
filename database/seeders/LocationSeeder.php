<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Paris',
                'Latitude' => 48.856613,
                'Longitude' => 2.352222,
                'city' => 'Paris',
                'country' => 'France',
                'region' => 'Île-de-France',
                'IsPopular' => true,
            ],
            [
                'name' => 'New York',
                'Latitude' => 40.712776,
                'Longitude' => -74.005974,
                'city' => 'New York',
                'country' => 'USA',
                'region' => 'New York',
                'IsPopular' => true,
            ],
            [
                'name' => 'Tokyo',
                'Latitude' => 35.689487,
                'Longitude' => 139.691711,
                'city' => 'Tokyo',
                'country' => 'Japan',
                'region' => 'Kantō',
                'IsPopular' => true,
            ],
            [
                'name' => 'Cairo',
                'Latitude' => 30.044420,
                'Longitude' => 31.235712,
                'city' => 'Cairo',
                'country' => 'Egypt',
                'region' => 'Cairo Governorate',
                'IsPopular' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::updateOrCreate(
                ['name' => $location['name']],
                $location
            );
        }
    }
}
