<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Message;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Message $message): bool
    {
        // Only the recipient can mark a message as read
        return $message->claim->charity_id === $user->id || 
               $message->claim->donation->user_id === $user->id;
    }
} 