<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Service\Notification\Notifier\EmailNotifier;
use App\Service\Notification\Notifier\TelegramNotifier;
use App\Service\Notification\UserNotifiersParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserNotifiersParserTest extends KernelTestCase
{
    /** @dataProvider userDataProvider */
    public function testParseUserNotificationServices(User $user, array $expectedNotifiers)
    {
        $parser = new UserNotifiersParser();
        $notifiers = $parser->parseNotifiers($user);

        sort($notifiers);
        sort($expectedNotifiers);

        self::assertEquals($notifiers, $expectedNotifiers);
    }

    public function userDataProvider()
    {
        $noNotifiersUser = new User("", "", 0);
        $emailOnlyUser = new User("", "", 1);
        $telegramOnlyUser = new User("", "", 2);
        $emailTelegramUser = new User("", "", 3);

        return [
            [$noNotifiersUser, []],
            [$emailOnlyUser, [EmailNotifier::class]],
            [$telegramOnlyUser, [TelegramNotifier::class]],
            [$emailTelegramUser, [EmailNotifier::class, TelegramNotifier::class]],
        ];
    }
}