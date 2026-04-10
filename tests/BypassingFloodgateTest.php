<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Jobs\FlushNotifications;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class BypassingFloodgateTest extends TestCase
{
    #[Test]
    public function it_does_not_buffer_an_exempt_notification(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();

        // When
        $user->notify((new TestNotification)->withoutThrottling());

        // Then
        Bus::assertNotDispatched(FlushNotifications::class);
    }

    #[Test]
    public function it_does_not_dispatch_a_flush_job_for_subsequent_notifications_in_the_same_window(): void
    {
        // Given
        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();

        // When
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);

        // Then
        Bus::assertDispatchedTimes(FlushNotifications::class, 1);
    }
}
