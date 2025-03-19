<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Claim;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClaimPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Claim $claim): bool
    {
        // Only the charity or donor can view the claim
        return $claim->charity_id === $user->id || 
               $claim->donation->user_id === $user->id;
    }

    public function sendMessage(User $user, Claim $claim): bool
    {
        // Only the charity or donor can send messages
        return $claim->charity_id === $user->id || 
               $claim->donation->user_id === $user->id;
    }
} 