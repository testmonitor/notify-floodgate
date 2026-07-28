<?php

namespace TestMonitor\Floodgate\Concerns;

use TestMonitor\Floodgate\Notifications\Summary;

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
     * Return a grouped summary of multiple buffered notifications of this type.
     */
    abstract public function toSummary(array $items): Summary;
}
