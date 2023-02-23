<?php

namespace App\Service\Notifier;

interface NotificationServiceInterface
{
    function notify(mixed $payload);
}