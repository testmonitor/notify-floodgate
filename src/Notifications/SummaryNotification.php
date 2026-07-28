<?php

namespace TestMonitor\Floodgate\Notifications;

use Illuminate\Notifications\Notification;

class SummaryNotification extends Notification
{
    public function __construct(
        protected Summary $summary,
        protected array $notifications,
        protected array $channels,
    ) {}

    /*
     * Return the channels this summary notification should be sent on.
     */
    public function via(mixed $notifiable): array
    {
        return $this->channels;
    }

    /*
     * Build the database representation using the summary's registered builder.
     */
    public function toArray(mixed $notifiable): array
    {
        return $this->summary->resolveChannel('database', $notifiable, $this->notifications);
    }

    /*
     * Build the mail representation using the summary's registered builder.
     */
    public function toMail(mixed $notifiable): mixed
    {
        return $this->summary->resolveChannel('mail', $notifiable, $this->notifications);
    }

    /*
     * Delegate any other channel (e.g. toSlack, toBroadcast) to the summary's registered builder.
     */
    public function __call(string $method, array $parameters): mixed
    {
        $channel = lcfirst(substr($method, 2));

        return $this->summary->resolveChannel($channel, $parameters[0] ?? null, $this->notifications);
    }
}
