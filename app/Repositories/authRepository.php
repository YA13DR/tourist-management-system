<?php

namespace App\Repositories;

use App\Http\Requests\FeedBackRequest;
use App\Http\Requests\PayRequest;
use App\Http\Requests\RatingRequest;
use App\Interface\AuthInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\OTPRequest;
use App\Models\Admin;
use App\Models\Booking;
use App\Models\DiscountPoint;
use App\Models\FeedBack;
use App\Models\Payment;
use App\Models\PointRule;
use App\Models\Promotion;
use App\Models\Rating;
use App\Models\Tour;
use App\Models\User;
use App\Models\UserRank;
use App\Notifications\OTPNotification;
use App\Notifications\PaymentNotification;
use App\Notifications\TourAdminRequestNotification;
use Illuminate\Http\Request;
use Str;
use Twilio\Rest\Client;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authRepository implements AuthInterface
{
    use ApiResponse;
    public function payForBooking($id , PayRequest $request){
        $request->validated();
        $booking = Booking::find($id);

        if (!$booking) {
            return $this->error('Booking not found', 404);
        }

        if ($booking->paymentStatus == 2) {
            return $this->error('This booking is already paid', 400);
        }
    
        $amount = $request->amount;
    
        if ($amount < $booking->totalPrice) {
            return $this->error('Paid amount is less than required total price', 400);
        }
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
            'amount' => $amount,
            'paymentDate' => now(),
            'paymentMethod' => $request->paymentMethod,
            'transaction_id' => 'FAKE-' . strtoupper(Str::random(8)),
            'status' => 2, 
            'gateway_response' => json_encode(['message' => 'Fake payment completed']),
        ]);
    
        $booking->update(['paymentStatus' => 2]);

        $user = $booking->user; 
        $user->notify(new PaymentNotification(
            $payment->amount,
            $payment->paymentMethod,
            $payment->payment_reference
        ));
        return $this->success('Payment completed successfully', [
            'bookingReference' => $booking->bookingReference,
            'amount' => $payment->amount,
            'payment_method' => $payment->paymentMethod,
            'payment_reference' => $payment->payment_reference,
        ]);
    }

    public function UserRank(){

        $user = auth('sanctum')->user();
        if (!$user) {
            return $this->error('User not authenticated', 401);
        }
        $rank = UserRank::where('user_id',$user->id)->first();
        if ($rank) {
            return $this->success('Successful', [
                'rank' => $rank->points_earned,
            ], 200);
        }

        return $this->error('Error', 401);
    }
    public function discountPoints(){
        $discountActions = PointRule::select('action', 'points')->get();

    if ($discountActions->isEmpty()) {
        return $this->error('No available discount actions found', 404);
    }

    return $this->success('Available discount actions', [
        'discounts' => $discountActions->map(function ($item) {
            return [
                'action' => $item->action,
                'points' => $item->points,
            ];
        }),
    ], 200);
    }

    public function addRating(RatingRequest $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $request->validated($request->all());

        $existing = Rating::where([
            ['user_id', '=', $user->id],
            ['rating_type', '=', $request['rating_type']]
        ])->first();

        if ($existing) {
            return $this->error('You have already rated this booking for this entity type', 400);
        }

        Rating::create([
            'user_id'       => $user->id,
            'rating_type'   => $request['rating_type'],
            'entity_id'     => $request['entity_id'],
            'rating'        => $request['rating'],
            'comment'       => $request['comment'],
            'ratingdate'    => now(),
            'isVisible'     => true,
            'admin_response'=> null,
        ]);

        return $this->success('Rating submitted successfully');
    }
    public function submitFeedback(FeedBackRequest $request)
    {
        $request->validated($request->all());

        $feedback = FeedBack::create([
            'user_id' => Auth::id(),
            'feedback_text' => $request['feedback_text'],
            'feedback_type' => $request['feedback_type'],
            'feedback_date' => now(),
            'status' => 1
        ]);

        return $this->success('Feedback submitted successfully', $feedback);
    }
    public function getAvailablePromotions()
    {
        $now = now();

        $promotions = Promotion::where('isActive', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->get();

        return $this->success('Available promotions', $promotions);
    }

    public function requestTourAdmin(Request $request){

        $validated = $request->validate([
            'tour_name' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $user = auth()->user();

        $admin = Admin::where('role', 'admin')->where('section','tour')->first();
        if (!$admin) {
            return response()->json(['message' => 'No admin found'], 404);
        }

        $admin->notify(new TourAdminRequestNotification($user, $validated));

        return response()->json(['message' => 'Your request has been sent successfully.']);
    }

    public function login(LoginRequest $request){
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = Auth::user();
        if (!$user) {
            return $this->error('User not found', 404);
        }

        $token = $user->createToken('API token for ' . $user->email)->plainTextToken;

        return $this->success('Authenticated', [
            'token' => $token,
        ], 200);
    }

    public function signup(RegisterRequest $request){
        $request->validated($request->all());

        // $image = $request->file('photo')->getClientOriginalName();
        // $path = $request->file('photo')->storeAs('', $request->first_name . '_' . $image, 'profile');

        $user = User::create([
            'photo' => $request->photo,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'location' => $request->location,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if (!$user) {
            return $this->error('Registration failed', 400);
        }

        $token = $user->createToken('API token for ' . $user->email)->plainTextToken;

        if (!$token) {
            return $this->error('Unable to create token', 400);
        }

       
        $user->notify(new OTPNotification());

        return $this->success('Registration successful', [
            'token' => $token,
        ], 201);
    }

    public function OTPCode(OTPRequest $request){
        $request->validated($request->all());

        $user = auth()->user();

        if ($user && $request->code == $user->code && $user->isCodeValid()) {
            $user->resetCode();
            $user->update(['email_verified_at' => now()]);
            return $this->ok('Verified successfully', 200);
        }

        return $this->error('Invalid code', 401);
    }

    public function resendOTPCode(){
        $user = auth()->user();

        if ($user && $user->email_verified_at == null) {
            $user->generateCode();
            return $this->ok('OTP code resent successfully', 200);
        }

        return $this->error('You have already verified', 400);
    }

    public function logout(){
        $user = auth()->user();
        $user->currentAccessToken()->delete();

        return $this->ok('Logout successful', 200);
    }
}
