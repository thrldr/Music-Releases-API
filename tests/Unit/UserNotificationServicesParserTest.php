<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Services\Notifiers\Parser\UserNotificationServicesParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserNotificationServicesParserTest extends KernelTestCase
{
    public function testParseUserNotificationServices()
    {
        $parser = new UserNotificationServicesParser();
        $noNotifiersUser = new User("", "", 0);

        $noServices = $parser->parseNotificationServices($noNotifiersUser);
        self::assertEquals([], $noServices);
    }
}