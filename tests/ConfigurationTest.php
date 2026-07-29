<?php

namespace TestMonitor\Floodgate\Tests;

use PHPUnit\Framework\Attributes\Test;
use TestMonitor\Floodgate\Cache\FloodgateStore;
use TestMonitor\Floodgate\Tests\Notifications\TestNotification;

class ConfigurationTest extends TestCase
{
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
