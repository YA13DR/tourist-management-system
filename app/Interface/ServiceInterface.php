<?php

namespace App\Interface;

use App\Http\Requests\FeedBackRequest;
use App\Http\Requests\PayRequest;
use App\Http\Requests\RatingRequest;
use Illuminate\Http\Request;

interface ServiceInterface
{
    public function UserRank();
    public function discountPoints();
    public function addRating(RatingRequest $request);
    public function submitFeedback(FeedBackRequest $request);
    public function getAvailablePromotions();
    public function requestTourAdmin(Request $request);
}
