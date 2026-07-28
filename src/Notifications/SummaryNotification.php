<?php

namespace TestMonitor\Floodgate\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
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
     * Return the summary array for the database channel.
     */
    public function toArray(mixed $notifiable): array
    {
        return $this->summary->toArray();
    }

    /*
     * Build the mail representation, passing both the summary and per-item detail to the view.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $items = array_map(
            fn ($notification) => $notification->toArray($notifiable),
            $this->notifications
        );

        return (new MailMessage)
            ->subject($this->summary->subject ?? __('You have new notifications'))
            ->markdown('floodgate::summary', [
                'summary' => $this->summary,
                'items' => $items,
            ]);
    }
}
