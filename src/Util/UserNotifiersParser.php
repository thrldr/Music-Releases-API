<?php

namespace App\Util;

use App\Entity\User;
use App\Service\Notifier\EmailNotifier;
use App\Service\Notifier\NotifierInterface;

class UserNotifiersParser
{
    const EMAIL = 1;
    const TELEGRAM = 2;

    /** @return NotifierInterface[] */
    public function parseNotifiers(User $user): array
    {
        $binaryServices = $user->getNotifiers();
        $notifierServices = [];

        if ($binaryServices & self::EMAIL) {
            $notifierServices[] = new EmailNotifier();
        }

        if ($binaryServices & self::TELEGRAM) {
//            $notifierServices[] = new TelegramNotifier();
        }

        return $notifierServices;
    }
}