<?php

namespace TestMonitor\Floodgate\Notifications;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Conditionable;

class Summary implements Arrayable
{
    use Conditionable;

    /**
     * The title displayed above the summary message.
     */
    public ?string $title = null;

    /**
     * The summary message, using the same :placeholder syntax as toArray().
     */
    public string $message = '';

    /**
     * The replacement values for the message's :placeholders.
     */
    public array $data = [];

    /**
     * The mail subject. Falls back to the title when not set.
     */
    public ?string $subject = null;

    /**
     * The text for the summary mail's call-to-action button.
     */
    public ?string $actionText = null;

    /**
     * The URL for the summary mail's call-to-action button.
     */
    public ?string $actionUrl = null;

    /*
     * Set the title displayed above the summary message.
     */
    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /*
     * Set the summary message, using the same :placeholder syntax as toArray().
     */
    public function message(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /*
     * Merge additional replacement values for the message's :placeholders.
     */
    public function with(array|string $key, mixed $value = null): static
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /*
     * Set the mail subject.
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    /*
     * Set the call-to-action button's text and URL.
     */
    public function action(string $text, string $url): static
    {
        $this->actionText = $text;
        $this->actionUrl = $url;

        return $this;
    }

    /*
     * Return the summary as an array, suitable for the database channel.
     */
    public function toArray(): array
    {
        return array_merge($this->data, array_filter([
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->actionUrl,
        ], fn ($value) => ! is_null($value)));
    }
}
