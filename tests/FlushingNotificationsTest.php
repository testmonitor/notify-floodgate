<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Cache\FloodgateStore;
use TestMonitor\Floodgate\Jobs\FlushNotifications;
use TestMonitor\Floodgate\Notifications\SummaryNotification;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class FlushingNotificationsTest extends TestCase
{
    #[Test]
    public function it_sends_the_original_notification_when_only_one_was_buffered(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();
        $user->notify(new TestNotification);

        // When
        Notification::fake();
        Bus::dispatched(FlushNotifications::class)->first()->handle(app(FloodgateStore::class));

        // Then
        Notification::assertSentTo($user, TestNotification::class);
        Notification::assertNotSentTo($user, SummaryNotification::class);
    }

    #[Test]
    public function it_sends_a_summary_when_multiple_notifications_were_buffered(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);

        // When
        Notification::fake();
        Bus::dispatched(FlushNotifications::class)->first()->handle(app(FloodgateStore::class));

        // Then
        Notification::assertSentTo($user, SummaryNotification::class);
        Notification::assertNotSentTo($user, TestNotification::class);
    }

    #[Test]
    public function it_sends_originals_when_the_number_of_notifications_is_within_the_threshold(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();
        $user->notify(new TestNotification(threshold: 3));
        $user->notify(new TestNotification(threshold: 3));
        $user->notify(new TestNotification(threshold: 3));

        // When
        Notification::fake();
        Bus::dispatched(FlushNotifications::class)->first()->handle(app(FloodgateStore::class));

        // Then
        Notification::assertSentTo($user, TestNotification::class);
        Notification::assertNotSentTo($user, SummaryNotification::class);
    }

    #[Test]
    public function it_does_nothing_when_the_buffer_is_empty(): void
    {
        // Given
        Notification::fake();
        $user = $this->createUser();

        // When
        (new FlushNotifications($user, 'non-existent-key', ['mail']))->handle(app(FloodgateStore::class));

        // Then
        Notification::assertNothingSent();
    }
}
