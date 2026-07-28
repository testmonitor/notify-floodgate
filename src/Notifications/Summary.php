<?php

namespace TestMonitor\Floodgate\Notifications;

use Closure;
use Illuminate\Support\Traits\Conditionable;
use RuntimeException;

class Summary
{
    use Conditionable;

    /**
     * The channel-specific builders, keyed by channel name.
     */
    public array $channels = [];

    /*
     * Provide a builder for a specific channel.
     */
    public function channel(string $channel, Closure $callback): static
    {
        $this->channels[$channel] = $callback;

        return $this;
    }

    /*
     * Determine whether a builder is registered for the given channel.
     */
    public function hasChannel(string $channel): bool
    {
        return isset($this->channels[$channel]);
    }

    /*
     * Resolve the builder registered for the given channel.
     */
    public function resolveChannel(string $channel, mixed ...$parameters): mixed
    {
        if (! $this->hasChannel($channel)) {
            throw new RuntimeException(
                "No summary builder registered for channel [{$channel}]. " .
                "Use ->channel('{$channel}', ...) to define one."
            );
        }

        return ($this->channels[$channel])(...$parameters);
    }
}
