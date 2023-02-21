<?php

namespace App\Services\Notifier;

interface NotifierServiceInterface
{
    function notify(mixed $payload);
}