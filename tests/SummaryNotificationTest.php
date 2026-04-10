<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Notifications\SummaryNotification;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class SummaryNotificationTest extends TestCase
{
    #[Test]
    public function it_returns_the_configured_channels(): void
    {
        // Given
        $notification = new SummaryNotification([], [], ['mail', 'database']);

        // When
        $channels = $notification->via($this->createUser());

        // Then
        $this->assertEquals(['mail', 'database'], $channels);
    }

    #[Test]
    public function it_returns_the_summary_array(): void
    {
        // Given
        $summary = ['message' => ':count issues assigned', 'properties' => ['count' => 3]];
        $notification = new SummaryNotification($summary, [], ['mail']);
        $user = $this->createUser();

        // When
        $result = $notification->toArray($user);

        // Then
        $this->assertEquals($summary, $result);
    }

    #[Test]
    public function it_builds_a_mail_message_with_summary_and_items(): void
    {
        // Given
        $user = $this->createUser();
        $summary = ['message' => ':count issues assigned', 'properties' => ['count' => 2]];
        $notifications = [new TestNotification, new TestNotification];
        $notification = new SummaryNotification($summary, $notifications, ['mail']);

        // When
        $mail = $notification->toMail($user);

        // Then
        $this->assertInstanceOf(MailMessage::class, $mail);
        $this->assertEquals('You have new notifications', $mail->subject);
        $this->assertCount(2, $mail->viewData['items']);
        $this->assertEquals($summary, $mail->viewData['summary']);
    }
}
