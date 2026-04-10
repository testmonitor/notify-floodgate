<?php

namespace TestMonitor\Floodgate\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use TestMonitor\Floodgate\FloodgateServiceProvider;
use TestMonitor\Floodgate\Tests\Models\TestNotifiable;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [FloodgateServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('mail.default', 'array');
    }

    protected function createUser(): TestNotifiable
    {
        return new TestNotifiable;
    }
}
