<?php

namespace TestMonitor\Floodgate;

use Illuminate\Support\ServiceProvider;
use TestMonitor\Floodgate\Cache\FloodgateStore;

class FloodgateServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishConfig();
        $this->publishViews();
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/floodgate.php', 'floodgate');

        $this->app->bind(FloodgateStore::class, fn () => new FloodgateStore(
            cache: $this->app->make('cache')->store(config('floodgate.cache.store')),
            prefix: config('floodgate.cache.prefix', 'floodgate'),
        ));
    }

    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/floodgate.php' => config_path('floodgate.php'),
        ], 'floodgate-config');
    }

    protected function publishViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'floodgate');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/floodgate'),
        ], 'floodgate-views');
    }
}
