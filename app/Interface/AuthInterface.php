<?php

namespace App\Interface;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\OTPRequest;

interface AuthInterface
{
    public function UserRank();
    public function discountPoints();
    public function login(LoginRequest $request);
    public function signup(RegisterRequest $request);
    public function OTPCode(OTPRequest $request);
    public function resendOTPCode();
    public function logout();
}
