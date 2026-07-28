<?php

namespace TestMonitor\Floodgate\Tests;

use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Notifications\Summary;
use TestMonitor\Floodgate\Notifications\SummaryNotification;

class SummaryNotificationTest extends TestCase
{
    #[Test]
    public function it_returns_the_configured_channels(): void
    {
        // Given
        $notification = new SummaryNotification(new Summary, [], ['mail', 'database']);

        // When
        $channels = $notification->via($this->createUser());

        // Then
        $this->assertEquals(['mail', 'database'], $channels);
    }

    #[Test]
    public function it_builds_the_database_representation_via_the_registered_channel_builder(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)->channel('database', fn ($notifiable, $notifications) => ['count' => count($notifications)]);
        $notification = new SummaryNotification($summary, ['a', 'b'], ['database']);

        // When
        $result = $notification->toArray($user);

        // Then
        $this->assertEquals(['count' => 2], $result);
    }

    #[Test]
    public function it_builds_the_mail_representation_via_the_registered_channel_builder(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)->channel('mail', fn ($notifiable, $notifications) => 'a custom mailable');
        $notification = new SummaryNotification($summary, [], ['mail']);

        // When
        $result = $notification->toMail($user);

        // Then
        $this->assertEquals('a custom mailable', $result);
    }

    #[Test]
    public function it_delegates_arbitrary_channels_to_the_registered_channel_builder(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)->channel('slack', fn ($notifiable, $notifications) => 'a slack message');
        $notification = new SummaryNotification($summary, [], ['slack']);

        // When
        $result = $notification->toSlack($user);

        // Then
        $this->assertEquals('a slack message', $result);
    }
}
