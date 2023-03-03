<?php

namespace App\Service\Notification\Notifier;

use App\Entity\Album;
use App\Entity\User;

class TelegramNotifier implements NotifierInterface
{
    function notify(User $user, Album $newAlbum): void
    {
        // TODO: Implement notify() method.
    }
}