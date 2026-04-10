<?php

namespace TestMonitor\Floodgate\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SummaryNotification extends Notification
{
    public function __construct(
        protected array $summary,
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
        return $this->summary;
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
            ->subject($this->subject())
            ->view($this->view(), [
                'summary' => $this->summary,
                'items' => $items,
            ]);
    }

    /*
     * Return the subject line for the summary email.
     */
    protected function subject(): string
    {
        return 'You have new notifications';
    }

    /*
     * Return the view used to render the summary email.
     */
    protected function view(): string
    {
        return 'floodgate::summary';
    }
}
