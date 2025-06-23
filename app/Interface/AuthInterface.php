<?php

namespace App\Interface;
use App\Http\Requests\FeedBackRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PayRequest;
use App\Http\Requests\RatingRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\OTPRequest;
use Illuminate\Http\Request;

interface AuthInterface
{
    public function login(LoginRequest $request);
    public function signup(RegisterRequest $request);
    public function OTPCode(OTPRequest $request);
    public function resendOTPCode();
    public function logout();
}
