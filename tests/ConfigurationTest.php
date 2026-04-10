<?php

namespace TestMonitor\Floodgate\Tests;

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Cache\FloodgateStore;
use TestMonitor\Floodgate\Jobs\FlushNotifications;
use TestMonitor\Floodgate\Tests\Notifications\CustomSummaryNotification;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class ConfigurationTest extends TestCase
{
    #[Test]
    public function it_uses_a_custom_summary_notification_class(): void
    {
        // Given
        config(['floodgate.summary' => CustomSummaryNotification::class]);

        Bus::fake([FlushNotifications::class]);
        $user = $this->createUser();
        $user->notify(new TestNotification);
        $user->notify(new TestNotification);

        // When
        Notification::fake();
        Bus::dispatched(FlushNotifications::class)->first()->handle(app(FloodgateStore::class));

        // Then
        Notification::assertSentTo($user, CustomSummaryNotification::class);
    }

    #[Test]
    public function it_uses_the_configured_cache_prefix_when_building_keys(): void
    {
        // Given
        config(['floodgate.cache.prefix' => 'custom-prefix']);
        $user = $this->createUser();

        // When
        $key = app(FloodgateStore::class)->buildKey($user, new TestNotification, ['mail']);

        // Then
        $this->assertStringStartsWith('custom-prefix.', $key);
    }
}
