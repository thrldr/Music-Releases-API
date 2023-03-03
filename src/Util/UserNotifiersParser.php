<?php

namespace App\Util;

use App\Entity\User;
use App\Service\Notification\Notifier\EmailNotifier;
use App\Service\Notification\Notifier\TelegramNotifier;

class UserNotifiersParser
{
    const EMAIL = 1;
    const TELEGRAM = 2;

    /** @return string[] */
    public function parseNotifiers(User $user): array
    {
        $binaryServices = $user->getNotifiers();
        $notifierServices = [];

        if ($binaryServices & self::EMAIL) {
            $notifierServices[] = EmailNotifier::class;
        }

        if ($binaryServices & self::TELEGRAM) {
            $notifierServices[] = TelegramNotifier::class;
        }

        return $notifierServices;
    }
}