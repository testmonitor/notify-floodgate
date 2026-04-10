<?php

namespace TestMonitor\Floodgate\Cache;

use Closure;
use Illuminate\Cache\Repository;
use Illuminate\Support\Str;

class FloodgateStore
{
    public function __construct(protected Repository $cache, protected string $prefix) {}

    /*
     * Store a notification and invoke the callback if this is the first push in the current window.
     */
    public function push(string $key, mixed $notification, int $delay, Closure $onFirstPush): void
    {
        $this->cache
            ->lock(name: $this->writeKey($key), seconds: 5)
            ->block(seconds: 2, callback: function () use ($key, $notification, $delay, $onFirstPush) {
                $this->appendItem($key, $notification);

                if (! $this->isPending($key)) {
                    $this->markPending($key, $delay);

                    $onFirstPush();
                }
            });
    }

    /*
     * Retrieve and remove all buffered notifications for the given key.
     */
    public function pull(string $key): array
    {
        $items = $this->cache->pull($this->itemsKey($key), []);

        $this->cache->forget($this->pendingKey($key));

        return array_map(fn ($payload) => $this->unserialize($payload), $items);
    }

    /*
     * Build a unique cache key for the given notifiable, notification and channels.
     */
    public function buildKey(mixed $notifiable, mixed $notification, array $channels): string
    {
        return $this->prefix . '.'
            . Str::snake(class_basename($notification))
            . '.' . $notifiable->getMorphClass()
            . '.' . $notifiable->getKey()
            . '.' . implode('_', $channels);
    }

    /*
     * Append a serialized notification to the cache list for the given key.
     */
    protected function appendItem(string $key, mixed $notification): void
    {
        $items = $this->cache->get($this->itemsKey($key), []);
        $items[] = $this->serialize($notification);

        $this->cache->put($this->itemsKey($key), $items, now()->addMinutes(10));
    }

    /*
     * Determine whether a flush job is already pending for the given key.
     */
    protected function isPending(string $key): bool
    {
        return $this->cache->has($this->pendingKey($key));
    }

    /*
     * Mark the given key as pending for the duration of the delay window.
     */
    protected function markPending(string $key, int $delay): void
    {
        $this->cache->put($this->pendingKey($key), true, now()->addSeconds($delay + 5));
    }

    /*
     * Serialize a notification for cache storage.
     */
    protected function serialize(mixed $notification): string
    {
        return serialize($notification);
    }

    /*
     * Unserialize a notification from cache storage.
     */
    protected function unserialize(string $payload): mixed
    {
        return unserialize($payload);
    }

    /*
     * Return the cache key used to store buffered notification items.
     */
    protected function itemsKey(string $key): string
    {
        return $key . ':items';
    }

    /*
     * Return the cache key used to track whether a flush is pending.
     */
    protected function pendingKey(string $key): string
    {
        return $key . ':pending';
    }

    /*
     * Return the cache key used for the write lock.
     */
    protected function writeKey(string $key): string
    {
        return $key . ':write';
    }
}
