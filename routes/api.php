  <?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//login
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/signup',[AuthController::class,'signup']);
//user
Route::middleware('auth:sanctum')->group(function () {
  //point and Rank
  Route::get('/auth/userRank',[AuthController::class,'userRank']);
  Route::get('/auth/discountPoints',[AuthController::class,'discountPoints']);
  //Rating
  Route::post('/auth/addRating',[AuthController::class,'addRating']);
  //FeedBack
  Route::post('/auth/submitFeedback',[AuthController::class,'submitFeedback']);
  //promotion
  Route::get('/auth/getAvailablePromotions',[AuthController::class,'getAvailablePromotions']);
  //payment
  Route::post('/auth/payForBooking/{id}',[AuthController::class,'payForBooking']);
});

// // Package Management Routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('travel/showAll',[TravelController::class,'getAllFlights']);
  Route::get('travel/show/{id}',[TravelController::class,'getFlight']);
  Route::get('travel/showAviable',[TravelController::class,'getAvailableFlights']);
  Route::get('travel/showAviableDate',[TravelController::class,'getAvailableFlightsDate']);
  Route::get('travel/showAgency/{id}',[TravelController::class,'getAgency']);
  Route::get('travel/showAllAgency',[TravelController::class,'getAllAgency']);
  Route::post('travel/bookFlight/{id}',[TravelController::class,'bookFlight']);
  Route::post('travel/bookFlightByPoint/{id}',[TravelController::class,'bookFlightByPoint']);
});
// // Package Management Routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('package/show/{id}',[PackageController::class,'showpackage']);
  Route::get('package/showAll',[PackageController::class,'showAllpackages']);
  Route::get('package/showAllAgency',[PackageController::class,'showAllAgency']);
  Route::get('package/showAll/{id}',[PackageController::class,'showAllpackagesAgency']);
  Route::post('package/bookPackage/{id}',[PackageController::class,'bookTravelPackage']);
});

// // Favourite Management Routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('favourite/show/{id}',[FavouriteController::class,'showFavourite']);
  Route::get('favourite/showAll',[FavouriteController::class,'showAllFavourite']);
  Route::post('/favourite/restaurant/{id}', [FavouriteController::class, 'addRestaurantToFavourite']);
  Route::post('/favourite/hotel/{id}', [FavouriteController::class, 'addHotelToFavourite']);
  Route::post('/favourite/tour/{id}', [FavouriteController::class, 'addTourToFavourite']);
  Route::post('/favourite/package/{id}', [FavouriteController::class, 'addPackageToFavourite']);
  Route::post('/favourite/delete/{id}', [FavouriteController::class, 'removeFromFavouriteById']);
});

// // Tour Management Routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('tour/show/{id}',[TourController::class,'showTour']);
  Route::get('tour/showAll',[TourController::class,'showAllTour']);
  Route::post('tour/bookTour/{id}',[TourController::class,'bookTour']);
  Route::post('tour/bookTourByPoint/{id}',[TourController::class,'bookTour']);
});

// // Hotel Management Routes
Route::middleware('auth:sanctum')->group(function () {
  Route::get('hotel/show/{id}',[HotelController::class,'showHotel']);
  Route::get('hotel/showAll',[HotelController::class,'showAllHotel']);
  Route::get('hotel/showNearBy',[HotelController::class,'showNearByHotel']);
  Route::get('hotel/showAviableRoom/{id}',[HotelController::class,'showAviableRoom']);
  Route::post('hotel/showAviableRoomType/{id}',[HotelController::class,'showAviableRoomType']);
  Route::get('hotel/showHistory',[HotelController::class,'showHistory']);
  Route::get('hotel/showReservationRoom',[HotelController::class,'showReservationRoom']);
  Route::post('hotel/bookHotel/{id}',[HotelController::class,'bookHotel']);
});

// Restaurant Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('restaurants/show/{id}',[RestaurantController::class,'showRestaurant']);
    Route::get('restaurants/showAll',[RestaurantController::class,'showAllRestaurant']);
    Route::get('restaurants/showNearBy',[RestaurantController::class,'showNearByRestaurant']);
    Route::get('restaurants/showMenuItem/{id}',[RestaurantController::class,'showMenuItem']);
    Route::get('restaurants/showMenuCategory/{id}',[RestaurantController::class,'showMenuCategory']);
    Route::get('restaurants/showAviableTable/{id}',[RestaurantController::class,'showAviableTable']);
    Route::post('restaurants/bookTable/{id}',[RestaurantController::class,'bookTable']);
    Route::post('restaurants/bookTableWithPromotion/{id}',[RestaurantController::class,'bookTableWithPromotion']);
    Route::post('restaurants/addOrder/{id}',[RestaurantController::class,'addOrder']);
});

// // Location Management Routes
// Route::prefix('locations')->group(function () {
//     // Routes will be implemented here
// });

// // Taxi Service Management Routes
// Route::prefix('taxi-services')->group(function () {
//     // Routes will be implemented here
// });

