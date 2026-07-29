<?php

namespace TestMonitor\Floodgate\Tests\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use TestMonitor\Floodgate\Concerns\Gateable;
use TestMonitor\Floodgate\Middleware\ThrottlesNotifications;

class TestNotification extends Notification implements ShouldQueue
{
    use Gateable, Queueable;

    public function __construct(public readonly int $threshold = 1) {}

    public function middleware(): array
    {
        return [new ThrottlesNotifications(threshold: $this->threshold)];
    }

    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)->line('Test notification');
    }

    public function toArray(mixed $notifiable): array
    {
        return ['message' => 'Test notification'];
    }

    public function toSummary(array $notifications, array $channels): Notification
    {
        return new TestSummaryNotification($notifications, $channels);
    }
}
