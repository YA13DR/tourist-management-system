<?php

namespace App\Repositories;

use App\Interface\AuthInterface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\OTPRequest;
use App\Models\User;
use App\Notifications\OTPNotification;
use Twilio\Rest\Client;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class authRepository implements AuthInterface
{
    use ApiResponse;

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
