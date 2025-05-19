<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedBackRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\PayRequest;
use App\Http\Requests\RatingRequest;
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
    public function payForBooking($id,PayRequest $request)
    {
        return $this->authRepository->payForBooking($id,$request);
    }
    public function UserRank()
    {
        return $this->authRepository->UserRank();
    }
    public function discountPoints()
    {
        return $this->authRepository->discountPoints();
    }
    public function addRating(RatingRequest $request)
    {
        return $this->authRepository->addRating($request);
    }
    public function submitFeedback(FeedBackRequest $request)
    {
        return $this->authRepository->submitFeedback($request);
    }
    public function getAvailablePromotions()
    {
        return $this->authRepository->getAvailablePromotions();
    }
    public function requestTourAdmin(Request $request)
    {
        return $this->authRepository->requestTourAdmin($request);
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
