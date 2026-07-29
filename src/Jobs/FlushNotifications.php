<?php

namespace TestMonitor\Floodgate\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TestMonitor\Floodgate\Cache\FloodgateStore;

class FlushNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected mixed $notifiable,
        protected string $key,
        protected array $channels,
        protected int $threshold = 1,
    ) {}

    /*
     * Pull buffered notifications and send them as originals or a summary.
     */
    public function handle(FloodgateStore $store): void
    {
        $notifications = $store->pull($this->key);

        if (empty($notifications)) {
            return;
        }

        if (count($notifications) <= $this->threshold) {
            foreach ($notifications as $notification) {
                $this->sendOriginal($notification);
            }

            return;
        }

        $this->sendSummary($notifications);
    }

    /*
     * Send the original notification, marked as exempt to prevent re-buffering.
     */
    protected function sendOriginal(mixed $notification): void
    {
        $this->notifiable->notify($notification->withoutThrottling());
    }

    /*
     * Send a summary notification grouping all buffered notifications.
     */
    protected function sendSummary(array $notifications): void
    {
        $this->notifiable->notify($notifications[0]->toSummary($notifications, $this->channels));
    }
}
