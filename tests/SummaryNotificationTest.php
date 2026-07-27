<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Notifications\Messages\MailMessage;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Notifications\Summary;
use TestMonitor\Floodgate\Notifications\SummaryNotification;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

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
    public function it_returns_the_summary_array(): void
    {
        // Given
        $summary = (new Summary)->message(':count issues assigned')->with(['count' => 3]);
        $notification = new SummaryNotification($summary, [], ['mail']);
        $user = $this->createUser();

        // When
        $result = $notification->toArray($user);

        // Then
        $this->assertEquals(['message' => ':count issues assigned', 'data' => ['count' => 3]], $result);
    }

    #[Test]
    public function it_builds_a_mail_message_with_summary_and_items(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)->message(':count issues assigned')->with(['count' => 2]);
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

    #[Test]
    public function it_uses_the_custom_subject_from_the_summary(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)
            ->message(':count issues assigned')
            ->with(['count' => 2])
            ->subject('Your issue activity summary');
        $notification = new SummaryNotification($summary, [], ['mail']);

        // When
        $mail = $notification->toMail($user);

        // Then
        $this->assertEquals('Your issue activity summary', $mail->subject);
    }

    #[Test]
    public function it_includes_the_title_in_the_rendered_mail(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)
            ->title('Issue Activity')
            ->message(':count issues assigned')
            ->with(['count' => 2]);
        $notification = new SummaryNotification($summary, [], ['mail']);

        // When
        $rendered = $notification->toMail($user)->render();

        // Then
        $this->assertStringContainsString('Issue Activity', $rendered);
        $this->assertStringContainsString('2 issues assigned', $rendered);
    }

    #[Test]
    public function it_renders_the_action_button_when_set(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)
            ->message(':count issues assigned')
            ->with(['count' => 2])
            ->action('View Issues', 'https://example.test/issues');
        $notification = new SummaryNotification($summary, [], ['mail']);

        // When
        $rendered = $notification->toMail($user)->render();

        // Then
        $this->assertStringContainsString('class="action"', $rendered);
        $this->assertStringContainsString('View Issues', $rendered);
        $this->assertStringContainsString('https://example.test/issues', $rendered);
    }

    #[Test]
    public function it_omits_the_action_button_when_not_set(): void
    {
        // Given
        $user = $this->createUser();
        $summary = (new Summary)->message(':count issues assigned')->with(['count' => 2]);
        $notification = new SummaryNotification($summary, [], ['mail']);

        // When
        $rendered = $notification->toMail($user)->render();

        // Then
        $this->assertStringNotContainsString('class="action"', $rendered);
    }
}
