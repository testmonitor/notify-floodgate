<?php

namespace TestMonitor\Floodgate\Concerns;

trait SummarizesNotifications
{
    public array $notifications;

    public array $channels;

    /*
     * Return exactly the channels this batch was buffered for.
     */
    public function via(mixed $notifiable): array
    {
        return $this->channels;
    }
}
