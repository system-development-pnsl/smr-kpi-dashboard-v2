<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SystemNotification extends Notification
{
    public function __construct(
        private string $message,
        private string $body = '',
        private string $url = '',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return array_filter([
            'message' => $this->message,
            'body'    => $this->body,
            'url'     => $this->url,
        ]);
    }
}
