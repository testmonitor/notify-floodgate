<?php

namespace TestMonitor\Floodgate\Concerns;

use Illuminate\Notifications\Notification;

trait Gateable
{
    protected bool $floodgateExempt = false;

    /*
     * Mark this notification to bypass the floodgate and send immediately.
     */
    public function withoutThrottling(): static
    {
        $this->floodgateExempt = true;

        return $this;
    }

    /*
     * Determine whether this notification is exempt from the floodgate.
     */
    public function isFloodgateExempt(): bool
    {
        return $this->floodgateExempt;
    }

    /*
     * Return a notification that summarizes multiple buffered notifications of this type.
     * $channels are the channels this summary must be sent on (the ones this batch was buffered for).
     */
    abstract public function toSummary(array $notifications, array $channels): Notification;
}
