<?php

namespace App\Service\Notification;

class NotificationDto
{
    public function __construct(
        public string $headline,
        public string $body,
    )
    {
    }

    public function toArray(): array
    {
        return [
            "headline" => $this->headline,
            "body" => $this->body,
        ];
    }
}