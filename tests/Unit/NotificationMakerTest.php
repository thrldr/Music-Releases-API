<?php

namespace App\Tests\Unit;

use App\Entity\Album;
use App\Entity\Band;
use App\Service\Notification\NotificationDto;
use App\Service\Notification\NotificationMaker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class NotificationMakerTest extends KernelTestCase
{
    /** @dataProvider userDataProvider */
    public function testMakeNotifications(Band $band, NotificationDto $expectedNotification)
    {
        $notificationMaker = new NotificationMaker();
        $notification = $notificationMaker->make($band);
        self::assertEquals($notification, $expectedNotification);
    }

    public function userDataProvider()
    {
        $band = new Band("Burzum");
        $album = new Album("Filosofem", date_create("2013-03-15"));
        $band->setLatestAlbum($album);

        $notification = new NotificationDto(
            "Burzum released a new album!",
            "'Filosofem' by Burzum was released on 15-03-2013"
        );
        return [
            [$band, $notification],
        ];

    }
}