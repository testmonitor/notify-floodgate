<?php

namespace TestMonitor\Floodgate\Tests\Models;

use Illuminate\Notifications\Notifiable;

class TestNotifiable
{
    use Notifiable;

    public function getKey(): int
    {
        return 1;
    }

    public function getMorphClass(): string
    {
        return 'notifiable';
    }

    public function routeNotificationForMail(): string
    {
        return 'test@example.com';
    }
}
