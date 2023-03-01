<?php

namespace App\Service\Notifier;

use App\Entity\Album;
use App\Entity\User;

interface NotifierInterface
{
    function notify(User $user, Album $newAlbum);
}