<?php

namespace App\Observers;

use App\Models\ExchangeRequest;
use App\Notifications\ExchangeRequestNotification;

class ExchangeRequestObserver
{
    public function created(ExchangeRequest $exchangeRequest): void
    {
        $exchangeRequest->receiver->notify(new ExchangeRequestNotification($exchangeRequest->id, 'created'));
    }

    public function updated(ExchangeRequest $exchangeRequest): void
    {
        if (! $exchangeRequest->wasChanged('status')) {
            return;
        }

        $event = match ([$exchangeRequest->getOriginal('status'), $exchangeRequest->status]) {
            ['pending', 'accepted'] => 'accepted',
            ['pending', 'rejected'] => 'rejected',
            ['pending', 'cancelled'] => 'cancelled',
            ['accepted', 'completed'] => 'completed',
            default => null,
        };

        if ($event === null) {
            return;
        }

        $recipients = match ($event) {
            'accepted', 'rejected' => [$exchangeRequest->sender],
            'cancelled' => [$exchangeRequest->receiver],
            'completed' => [$exchangeRequest->sender, $exchangeRequest->receiver],
            default => [],
        };

        foreach ($recipients as $recipient) {
            $recipient->notify(new ExchangeRequestNotification($exchangeRequest->id, $event));
        }
    }
}
