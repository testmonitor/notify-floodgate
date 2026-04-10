<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Jobs\FlushNotifications;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class BufferingNotificationsTest extends TestCase
{
    #[Test]
    public function it_buffers_a_notification_instead_of_sending_it_immediately(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();

        // When
        $user->notify(new TestNotification);

        // Then
        Bus::assertDispatched(FlushNotifications::class);
    }

    #[Test]
    public function it_dispatches_a_single_flush_job_for_multiple_notifications(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();

        // When
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);

        // Then
        Bus::assertDispatchedTimes(FlushNotifications::class, 1);
    }
}
