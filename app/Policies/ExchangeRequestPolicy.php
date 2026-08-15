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

    public function accept(User $user, ExchangeRequest $exchangeRequest): bool
    {
        return $user->role === 'student'
            && $exchangeRequest->status === 'pending'
            && $exchangeRequest->receiver_id === $user->id;
    }

    public function reject(User $user, ExchangeRequest $exchangeRequest): bool
    {
        return $this->accept($user, $exchangeRequest);
    }

    public function cancel(User $user, ExchangeRequest $exchangeRequest): bool
    {
        return $user->role === 'student'
            && $exchangeRequest->status === 'pending'
            && $exchangeRequest->sender_id === $user->id;
    }

    public function complete(User $user, ExchangeRequest $exchangeRequest): bool
    {
        return $user->role === 'student'
            && $exchangeRequest->status === 'accepted'
            && ($exchangeRequest->sender_id === $user->id || $exchangeRequest->receiver_id === $user->id);
    }
}
