<?php

namespace App\Policies;

use App\Models\ExchangeRequest;
use App\Models\User;

class ExchangeRequestPolicy
{
    public function view(User $user, ExchangeRequest $exchangeRequest): bool
    {
        return $user->role === 'student'
            && ($exchangeRequest->sender_id === $user->id || $exchangeRequest->receiver_id === $user->id);
    }
}
