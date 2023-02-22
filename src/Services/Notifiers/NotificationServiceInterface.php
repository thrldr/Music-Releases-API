<?php

namespace App\Services\Notifiers;

interface NotificationServiceInterface
{
    function notify(mixed $payload);
}