<?php

namespace App\Service\Notifier;

interface NotifierInterface
{
    function notify(mixed $payload);
}