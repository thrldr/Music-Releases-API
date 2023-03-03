<?php

namespace App\Service\Notification\Notifier;

use App\Entity\Album;
use App\Entity\User;

interface NotifierInterface
{
    function notify(User $user, Album $newAlbum): void;
}