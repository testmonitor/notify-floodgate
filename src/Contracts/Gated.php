<?php

namespace TestMonitor\Floodgate\Contracts;

use Illuminate\Notifications\Notification;

interface Gated
{
    /*
     * Mark this notification to bypass the floodgate and send immediately.
     */
    public function withoutThrottling(): static;

    /*
     * Determine whether this notification is exempt from the floodgate.
     */
    public function isFloodgateExempt(): bool;

    /*
     * Return a notification that summarizes multiple buffered notifications of this type.
     * $channels are the channels this summary must be sent on (the ones this batch was buffered for).
     */
    public function toSummary(array $notifications, array $channels): Notification;
}
