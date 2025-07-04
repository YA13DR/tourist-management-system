  <?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\TravelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//login
Route::post('/auth/login',[AuthController::class,'login']);
Route::post('/auth/signup',[AuthController::class,'signup']);

/////////////////////// user //////////////////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/resendOTPCode',[AuthController::class,'resendOTPCode']);
    Route::post('/auth/OTPCode',[AuthController::class,'OTPCode']);
    Route::post('/auth/logout',[AuthController::class,'logout']);
});

/////////////////////// Application //////////////////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  //point and Rank
  Route::get('/auth/userRank',[ServiceController::class,'userRank']);
  Route::get('/auth/discountPoints',[ServiceController::class,'discountPoints']);
  //Rating
  Route::post('/auth/addRating',[ServiceController::class,'addRating']);
  //FeedBack
  Route::post('/auth/submitFeedback',[ServiceController::class,'submitFeedback']);
  //promotion
  Route::get('/auth/getAvailablePromotions',[ServiceController::class,'getAvailablePromotions']);
  //requestTourAdmin
  Route::post('/auth/requestTourAdmin',[ServiceController::class,'requestTourAdmin']);
});

//////////////////// booking /////////////////////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
   //payment
  Route::post('/booking/payForBooking/{id}',[BookingController::class,'payForBooking']);
  Route::get('/booking/getAllBookings',[BookingController::class,'getAllBookings']);
  Route::get('/booking/getBookingHistory',[BookingController::class,'getBookingHistory']);
  Route::get('/booking/cancelBooking/{id}',[BookingController::class,'cancelBooking']);
  Route::post('/booking/modifyBooking/{id}',[BookingController::class,'modifyBooking']);
});

//////////////////// location ///////////////////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  Route::get('/location/show/{id}',[LocationController::class,'showLocation']);
  Route::get('/location/showAll',[LocationController::class,'showAllLocation']);
  Route::get('/location/showAllLocationFilter',[LocationController::class,'showAllLocationFilter']);
});

/////////////////// Package Management Routes ///////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  Route::get('travel/showAll',[TravelController::class,'getAllFlights']);
  Route::get('travel/show/{id}',[TravelController::class,'getFlight']);
  Route::get('travel/showAviable',[TravelController::class,'getAvailableFlights']);
  Route::post('travel/showAviableDate',[TravelController::class,'getAvailableFlightsDate']);
  Route::get('travel/showAgency/{id}',[TravelController::class,'getAgency']);
  Route::get('travel/showAllAgency',[TravelController::class,'getAllAgency']);
  Route::post('travel/bookFlight/{id}',[TravelController::class,'bookFlight']);
  Route::post('travel/bookFlightByPoint/{id}',[TravelController::class,'bookFlightByPoint']);
  Route::post('travel/updateFlightBooking/{id}',[TravelController::class,'updateFlightBooking']);
});

///////////////// Favourite Management Routes /////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  Route::get('favourite/show/{id}',[FavouriteController::class,'showFavourite']);
  Route::get('favourite/showAll',[FavouriteController::class,'showAllFavourite']);
  Route::post('/favourite/restaurant/{id}', [FavouriteController::class, 'addRestaurantToFavourite']);
  Route::post('/favourite/hotel/{id}', [FavouriteController::class, 'addHotelToFavourite']);
  Route::post('/favourite/tour/{id}', [FavouriteController::class, 'addTourToFavourite']);
  Route::post('/favourite/package/{id}', [FavouriteController::class, 'addPackageToFavourite']);
  Route::post('/favourite/delete/{id}', [FavouriteController::class, 'removeFromFavouriteById']);
});

////////////////// Tour Management Routes ////////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  Route::get('tour/show/{id}',[TourController::class,'showTour']);
  Route::get('tour/showAll',[TourController::class,'showAllTour']);
  Route::post('tour/bookTour/{id}',[TourController::class,'bookTour']);
  Route::post('tour/bookTourByPoint/{id}',[TourController::class,'bookTourByPoint']);
});

/////////////////// Hotel Management Routes ////////////////////////////////////
Route::middleware('auth:sanctum')->group(function () {
  Route::get('hotel/show/{id}',[HotelController::class,'showHotel']);
  Route::get('hotel/showAll',[HotelController::class,'showAllHotel']);
  Route::get('hotel/showNearBy',[HotelController::class,'showNearByHotel']);
  Route::get('hotel/showAviableRoom/{id}',[HotelController::class,'showAviableRoom']);
  Route::post('hotel/showAviableRoomType/{id}',[HotelController::class,'showAviableRoomType']);
  Route::post('hotel/bookHotel/{id}',[HotelController::class,'bookHotel']);
});


/////////////////// Restaurant Management Routes//////////////////////////
Route::middleware('auth:sanctum')->group(function () {
    Route::get('restaurants/show/{id}',[RestaurantController::class,'showRestaurant']);
    Route::get('restaurants/showAll',[RestaurantController::class,'showAllRestaurant']);
    Route::get('restaurants/showNearBy',[RestaurantController::class,'showNearByRestaurant']);
    Route::get('restaurants/showRestaurantByLocation',[RestaurantController::class,'showRestaurantByLocation']);
    Route::get('restaurants/showMenuItem/{id}',[RestaurantController::class,'showMenuItem']);
    Route::get('restaurants/showMenuCategory/{id}',[RestaurantController::class,'showMenuCategory']);
    Route::get('restaurants/showAviableTable/{id}',[RestaurantController::class,'showAviableTable']);
    Route::post('restaurants/bookTable/{id}',[RestaurantController::class,'bookTable']);
});

