<?php

namespace App\Util;

use App\Entity\User;
use App\Service\Notifier\NotificationServiceInterface;

class UserNotificationServicesParser
{
    const EMAIL = 1;
    const TELEGRAM = 2;

    /** @return NotificationServiceInterface[] */
    public function parseNotificationServices(User $user): array
    {
        $binaryServices = $user->getNotificationServices();
        $notifierServices = [];

        if ($binaryServices & self::EMAIL) {
//            $notifierServices[] = new EmailNotifier();
        }

        if ($binaryServices & self::TELEGRAM) {
//            $notifierServices[] = new TelegramNotifier();
        }

        return $notifierServices;
    }
}