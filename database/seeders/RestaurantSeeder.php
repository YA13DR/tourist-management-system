<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\RestaurantImage;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RestaurantTable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        $restaurant = Restaurant::create([
            'name' => 'Al-Fakher Restaurant',
            'description' => 'A luxury restaurant offering a variety of dishes with excellent service.',
            'location_id' => 1, 
            'Latitude' => 24.7136,
            'Longitude' => 46.6753,
            'cuisine' => 'Saudi Cuisine',
            'priceRange' => 3,
            'openingTime' => '10:00:00',
            'closingTime' => '23:00:00',
            'averageRating' => 4.5,
            'totalRatings' => 150,
            'mainImageURL' => 'path/to/image.jpg',
            'website' => 'https://restaurant-website.com',
            'phone' => '+966123456789',
            'email' => 'info@restaurant.com',
            'max_tables' => 50,
            'cost' => 100.00,
            'isActive' => true,
            'isFeatured' => true,
            'admin_id' => 3, 
        ]);

        RestaurantImage::create([
            'restaurant_id' => $restaurant->id,
            'imageURL' => 'path/to/restaurant_image_1.jpg',
            'displayOrder' => 1,
            'caption' => 'Exterior view of the restaurant',
            'isActive' => true,
        ]);

        $category = MenuCategory::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Appetizers',
            'description' => 'Various appetizers',
            'displayOrder' => 1,
            'isActive' => true,
        ]);

        MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Mutabal',
            'description' => 'Fresh appetizers with tahini and olive oil.',
            'price' => 25.00,
            'isVegetarian' => true,
            'isVegan' => true,
            'isGlutenFree' => false,
            'spiciness' => 0,
            'imageURL' => 'path/to/item_image_1.jpg',
            'isActive' => true,
            'isFeatured' => false,
        ]);

        RestaurantTable::create([
            'restaurant_id' => $restaurant->id,
            'number' => 'T1',
            'cost' => 100,
            'location' => 'Indoor',
            'isActive' => true,
        ]);
    }
}
