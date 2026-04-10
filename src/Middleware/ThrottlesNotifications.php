<?php

namespace TestMonitor\Floodgate\Middleware;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Notifications\SendQueuedNotifications;
use TestMonitor\Floodgate\Cache\FloodgateStore;
use TestMonitor\Floodgate\Jobs\FlushNotifications;

class ThrottlesNotifications
{
    public function __construct(
        protected int $threshold = 1,
        protected ?int $delay = null,
    ) {}

    /*
     * Intercept the queued notification job and buffer it, or pass it through if exempt.
     */
    public function handle(SendQueuedNotifications $job, Closure $next): void
    {
        if ($this->isExempt($job->notification)) {
            $next($job);

            return;
        }

        $store = Container::getInstance()->make(FloodgateStore::class);
        $delay = $this->delay ?? config('floodgate.delay', 10);

        foreach ($job->notifiables as $notifiable) {
            $this->throttleFor($store, $notifiable, $job->notification, $job->channels, $delay);
        }
    }

    /*
     * Determine whether the notification is exempt from throttling.
     */
    protected function isExempt(mixed $notification): bool
    {
        return method_exists($notification, 'isFloodgateExempt') && $notification->isFloodgateExempt();
    }

    /*
     * Push the notification into the store and schedule a flush job on the first push.
     */
    protected function throttleFor(
        FloodgateStore $store,
        mixed $notifiable,
        mixed $notification,
        array $channels,
        int $delay
    ): void {
        $key = $store->buildKey($notifiable, $notification, $channels);

        $store->push(
            $key,
            notification: $notification,
            delay: $delay,
            onFirstPush: function () use ($notifiable, $key, $channels, $delay) {
                FlushNotifications::dispatch($notifiable, $key, $channels, $this->threshold)
                    ->delay(now()->addSeconds($delay));
            }
        );
    }
}
