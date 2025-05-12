<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourCategory;
use App\Models\TourSchedule;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1 = TourCategory::create([
            'name' => 'Adventure',
            'description' => 'Exciting and adventurous tours for thrill seekers.',
            'iconURL' => 'adventure_icon.png',
            'isActive' => true,
        ]);
        $tour1 = Tour::create([
            'name' => 'Mountain Adventure',
            'description' => 'A thrilling mountain trekking experience.',
            'shortDescription' => 'A challenging trek through the mountains.',
            'location_id' => 1, 
            'durationHours' => 8.5,
            'durationDays' => 1,
            'basePrice' => 150.00,
            'discountPercentage' => 10,
            'maxCapacity' => 20,
            'minParticipants' => 5,
            'difficultyLevel' => 3,
            'averageRating' => 4.5,
            'totalRatings' => 100,
            'isActive' => true,
            'isFeatured' => true,
            'admin_id' => 7,
        ]);

        $schedule1 = TourSchedule::create([
            'tour_id' => $tour1->id,
            'startDate' => '2025-06-01',
            'endDate' => '2025-06-01',
            'startTime' => '08:00:00',
            'availableSpots' => 20,
            'price' => 150.00,
            'isActive' => true,
        ]);
        $activity1 = Activity::create([
            'name' => 'Hiking',
            'description' => 'A challenging and exciting hiking experience.',
            'image' => 'hiking_image.png',
        ]);
        $activity2 = Activity::create([
            'name' => 'Snorkeling',
            'description' => 'A fun and relaxing snorkeling adventure.',
            'image' => 'snorkeling_image.png',
        ]);
        TourActivity::create([
            'schedule_id' => $schedule1->id,
            'activity_id' => $activity1->id,
            'isActive' => true,
        ]);

        DB::table('TourCategoryMapping')->insert([
            ['tour_id' => $tour1->id, 'category_id' => $category1->id],
        ]);
    }
}
