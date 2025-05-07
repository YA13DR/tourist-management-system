<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\RegisterRequest;
use App\Interface\AuthInterface;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authRepository;
    public function __construct(AuthInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }
    public function login(LoginRequest $request)
    {
        return $this->authRepository->login($request);
    }

    public function signup(RegisterRequest $request)
    {
        return $this->authRepository->signup($request);
    }

    public function OTPCode(OTPRequest $request)
    {
        return $this->authRepository->OTPCode($request);
    }

    public function resendOTPCode()
    {
        return $this->authRepository->resendOTPCode();
    }

    public function logout()
    {
        return $this->authRepository->logout();
    }
}
