<?php

namespace TestMonitor\Floodgate\Contracts;

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
     * Return a grouped summary of multiple buffered notifications of this type.
     */
    public function toSummary(array $items): array;
}
