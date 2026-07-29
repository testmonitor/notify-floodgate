<?php

namespace TestMonitor\Floodgate\Tests\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use TestMonitor\Floodgate\Concerns\SummarizesNotifications;

class TestSummaryNotification extends Notification
{
    use SummarizesNotifications;

    public function __construct(public array $notifications, public array $channels) {}

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)->line(count($this->notifications) . ' test notifications');
    }

    public function toArray(mixed $notifiable): array
    {
        return ['count' => count($this->notifications)];
    }
}
