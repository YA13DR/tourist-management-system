<?php

namespace App\Traits;

use App\Models\PointRule;
use App\Models\Rank;
use App\Models\UserRank;

trait HandlesUserPoints
{
    public function addPointsFromAction($user, string $action, int $quantity = 1): void
    {
        $rule = PointRule::where('action', $action)->first();

        if (!$rule) {
            return; 
        }

        $pointsToAdd = $rule->points * $quantity;

        $userRank = $user->rank ?? new UserRank(['user_id' => $user->id]);

        $userRank->points_earned += $pointsToAdd;

        $userRank->rank_id = Rank::where('min_points', '<=', $userRank->points_earned)
            ->orderByDesc('min_points')
            ->first()?->id;

        $userRank->save();
    }
}
