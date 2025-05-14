<?php

namespace App\Repositories;

use App\Http\Requests\RestaurantBookingRequest;
use App\Http\Requests\RestaurantOrderRequest;
use App\Interface\AuthInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\OTPRequest;
use App\Interface\RestaurantInterface;
use App\Models\Booking;
use App\Models\Favourite;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Promotion;
use App\Models\Restaurant;
use App\Models\RestaurantBooking;
use App\Models\RestaurantTable;
use App\Models\User;
use App\Traits\HandlesUserPoints;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RestaurantRepository implements RestaurantInterface
{
    use ApiResponse,HandlesUserPoints;

    public function showRestaurant($id)
        {
            $restaurant = Restaurant::with('images', 'menuCategories.menuItems')
                                ->where('id', $id)
                                ->first();

            if (!$restaurant) {
                return $this->error('Restaurant not found', 404);
            }
            $user = auth()->user();

            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $restaurant->id,
                    'favoritable_type' => Restaurant::class,
                ])->exists();
            }
            $now = now();
            $promotion = Promotion::where('isActive', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 5) 
                ->first();
            $result = [];
            foreach ($restaurant->menuCategories as $menuCategorie) {
                $restaurantData = [
                    'category ' => $menuCategorie->name,
                ];

                foreach ($menuCategorie->menuItems as $menuItem) {
                    if ($menuItem->restaurant_id == $restaurant->id) {
                        $menuItemData = [
                            'menuItem' => $menuItem->name,
                        ];
                        $restaurantData['menuItems'][] = $menuItemData;
                    }
                }
                $result[] = $restaurantData;
            }

            return $this->success('Store retrieved successfully', [
                'restaurant ' => $restaurant,
                'category'=>$result,
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
            ]);
        }

    public function showAllRestaurant(){
        $restaurants = Restaurant::with('menuCategories')->get();
        $result = $restaurants->map(function($restaurant) {
            $user = auth()->user();
            $isFavourited = false;
            if ($user) {
                $isFavourited = Favourite::where([
                    'user_id' => $user->id,
                    'favoritable_id' => $restaurant->id,
                    'favoritable_type' => Restaurant::class,
                ])->exists();
            }
            $now = now();
            $promotion = Promotion::where('isActive', true)
                ->where('start_date', '<=', $now)
                ->where('end_date', '>=', $now)
                ->where('applicable_type', 5) 
                ->first();
            return [
                'restaurant' => $restaurant,
                'categories' => $restaurant->menuCategories, 
                'is_favourited' => $isFavourited,
                'promotion' => $promotion ? [
                    'promotion_code' => $promotion->promotion_code,
                    'description' => $promotion->description,
                    'discount_type' => $promotion->discount_type,
                    'discount_value' => $promotion->discount_value,
                    'minimum_purchase' => $promotion->minimum_purchase,
                ] : null,
            ];
        });
        return $this->success('All restaurants retrieved successfully', [
            'restaurants' => $result,
        ]);
    }
    public function showNearByRestaurant(Request $request)
    {
        $latitude = $request->input('latitude');
    $longitude = $request->input('longitude');
    $radius = $request->input('radius', 5);

    $nearbyRestaurants = Restaurant::selectRaw(
        "id, restaurant_name, ST_Distance_Sphere(location, POINT(?, ?)) as distance",
        [$longitude, $latitude]
    )
    ->having('distance', '<=', $radius * 1000) 
    ->get();

    return $this->success('Nearby restaurants retrieved successfully', [
        'restaurants' => $nearbyRestaurants,
    ]);
    }
    public function showMenuCategory(){
        $categories = MenuCategory::with('restaurant','menuItems')->get();
        if (!$categories) {
            return $this->error('No categories found', 404);
        }
    
        return $this->success('categories retrieved successfully', [
            'categories' => $categories,
        ]);
    }
    public function showMenuItem($id){
        $category = MenuCategory::find($id);
        if ( !$category) {
            return $this->error('Menu item not found', 404);
        }
        $menuItem = MenuItem::where('category_id', $category->id)->first();

        if (!$menuItem || !$category) {
            return $this->error('Menu item not found', 404);
        }
    
        return $this->success('Menu item retrieved successfully', [
            'menu_item' => $menuItem,
        ]);
    }
    public function bookTableWithPromotion($id,RestaurantBookingRequest $request){
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }
        $reservationDate = $request->reservationDate;
        $reservationTime = $request->reservationTime;
        
        $maxTables = $restaurant->max_tables;
        
        $countReservations = RestaurantBooking::where('restaurant_id', $restaurant->id)
            ->where('reservationDate', $reservationDate)
            ->count();
        
        if ($countReservations >= $maxTables) {
            return $this->error('No tables available for this date', 400);
        }
        $bookingReference = 'RB-' . strtoupper(uniqid());
        $discount = $restaurant->discount ?? 0;  
        
        $totalPrice = $restaurant->cost;  

        $discountAmount = ($discount > 0) ? ($totalPrice * $discount / 100) : 0;
        $totalPriceAfterDiscount = $totalPrice - $discountAmount;

        $booking = Booking::create([
            'bookingReference' => $bookingReference,
            'user_id' => auth('sanctum')->id(),
            'bookingType' => 4, 
            'totalPrice' => $totalPriceAfterDiscount,
            'discountAmount' => $discountAmount,
            'paymentStatus' => 1,  
        ]);
        if (!$booking) {
            return $this->error('Failed to create booking', 500);
        }
        $tableReservation = RestaurantBooking::create([
            'booking_id'=>$booking->id,
            'user_id' => auth('sanctum')->id(),
            'restaurant_id' => $restaurant->id,
            'table_id' => rand(1, $restaurant->max_tables),
            'reservationDate' => $reservationDate,
            'reservationTime' => $reservationTime,
            'numberOfGuests' => $request->numberOfGuests,
            'cost' => $totalPriceAfterDiscount, 
        ]);
    
        $this->addPointsFromAction(auth('sanctum')->user(), 'book_restaurant', 1); 

        return $this->success('Table reserved successfully', [
            'reservation_id' => $tableReservation->id,
            'date' => $tableReservation->reservationDate,
            'time' => $tableReservation->reservationTime,
            'table_id' => $tableReservation->table_id,
            'cost' => $tableReservation->cost,
            'bookingReference' => $booking->bookingReference,
            'discountAmount' => $discountAmount,
        ]);
    }
    public function bookTable($id, RestaurantBookingRequest $request)
    {
        $restaurant = Restaurant::find($id);

    if (!$restaurant) {
        return $this->error('Restaurant not found', 404);
    }

    $reservationDate = $request->reservationDate;
    $reservationTime = $request->reservationTime;

    $maxTables = $restaurant->max_tables;
    $countReservations = RestaurantBooking::where('restaurant_id', $restaurant->id)
        ->where('reservationDate', $reservationDate)
        ->count();

    if ($countReservations >= $maxTables) {
        return $this->error('No tables available for this date', 400);
    }

    $bookingReference = 'RB-' . strtoupper(uniqid());
    $basePrice = $restaurant->cost;

    $promotion = null;
    $promotionCode = $request->promotion_code;

    if ($promotionCode) {
        $promotion = Promotion::where('promotion_code', $promotionCode)
            ->where('isActive', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($q) {
                $q->where('applicable_type', 1) 
                  ->orWhere('applicable_type', 5); 
            })
            ->first();

        if (!$promotion || !$promotion->isActive) {
            return $this->error('Invalid or expired promotion code', 400);
        }

        if ($basePrice < $promotion->minimum_purchase) {
            return $this->error("Total must be at least {$promotion->minimum_purchase} to use this code.", 400);
        }

        if (!in_array($promotion->applicable_type, [null, 1, 5])) {
            return $this->error('This code cannot be applied to this restaurant booking', 400);
        }
    }

    $discountAmount = 0;
    if ($promotion) {
        $discountAmount = $promotion->discount_type == 1
            ? ($basePrice * $promotion->discount_value / 100) 
            : $promotion->discount_value;

        $discountAmount = min($discountAmount, $basePrice);
    }

    $totalPriceAfterDiscount = $basePrice - $discountAmount;

    $booking = Booking::create([
        'bookingReference' => $bookingReference,
        'user_id' => auth('sanctum')->id(),
        'bookingType' => 4,
        'totalPrice' => $totalPriceAfterDiscount,
        'discountAmount' => $discountAmount,
        'paymentStatus' => 1,
    ]);

    if (!$booking) {
        return $this->error('Failed to create booking', 500);
    }

    $tableReservation = RestaurantBooking::create([
        'booking_id' => $booking->id,
        'user_id' => auth('sanctum')->id(),
        'restaurant_id' => $restaurant->id,
        'table_id' => rand(1, $restaurant->max_tables),
        'reservationDate' => $reservationDate,
        'reservationTime' => $reservationTime,
        'numberOfGuests' => $request->numberOfGuests,
        'cost' => $totalPriceAfterDiscount,
    ]);

    if ($promotion) {
        $promotion->increment('current_usage');
    }

    $this->addPointsFromAction(auth('sanctum')->user(), 'book_restaurant', 1);

    return $this->success('Table reserved successfully', [
        'reservation_id' => $tableReservation->id,
        'date' => $tableReservation->reservationDate,
        'time' => $tableReservation->reservationTime,
        'table_id' => $tableReservation->table_id,
        'cost' => $tableReservation->cost,
        'bookingReference' => $booking->bookingReference,
        'discountAmount' => $discountAmount,
    ]);
    }

    public function addOrder($id, RestaurantOrderRequest $request){
        $booking = RestaurantBooking::with('booking')->find($id);

        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        $orderItems = $request->orderItems;
        $finalOrder = [];
        $totalFoodCost = 0;

        foreach ($orderItems as $orderItem) {
            $menuItem = MenuItem::find($orderItem['item_id']);
            
            if (!$menuItem) {
                return $this->error("Menu item not found", 404);
            }

            $quantity = $orderItem['quantity'];
            $subtotal = $menuItem->price * $quantity;

            $finalOrder[] = [
                'item_id' => $menuItem->id,
                'name' => $menuItem->name,
                'quantity' => $quantity,
                'price' => $menuItem->price,
                'subtotal' => $subtotal,
            ];

            $totalFoodCost += $subtotal;  
        }
        $booking->order = json_encode($finalOrder);
        $booking->cost += $totalFoodCost;
        $booking->save();

        if ($booking->booking) {
            $booking->booking->totalPrice = $booking->cost;
            $booking->booking->save();
        }
        $this->addPointsFromAction(auth('sanctum')->user(), 'add_restaurant_order', count($orderItems));

        return $this->success('Order added successfully', [
            'reservation_id' => $booking->id,
            'order' => $finalOrder,
            'total_cost' => $booking->cost,
        ]);
    }
    public function showAviableTable($id){
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return $this->error('Restaurant not found', 404);
        }

        $availableTablesIndoor = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)  
        ->where('location',  'Indoor')  
        ->get();
        $availableTablesOutdoor = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)  
        ->where('location',  'Outdoor')  
        ->get();
        $availableTablesPrivate = RestaurantTable::where('restaurant_id', $restaurant->id)
        ->where('number', '>', 0)  
        ->where('location',  'Private')  
        ->get();

        return $this->success('Available tables retrieved successfully', [
            'available_tables_Inside' => $availableTablesIndoor,
            'available_tables_outdoor' => $availableTablesOutdoor,
            'available_tables_private' => $availableTablesPrivate,
        ]);
    }
}
