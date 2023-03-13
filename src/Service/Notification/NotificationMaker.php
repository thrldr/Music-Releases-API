<?php

namespace App\Service\Notification;

use App\Entity\Band;

class NotificationMaker
{
    public function make(Band $band): NotificationDto
    {
        $headline = $band->getName() . " released a new album!";
        $album = $band->getLatestAlbum();
        $body = "'" . $album->getName() . "' by " . $band->getName() . " was released on ";
        $body .= date_format($album->getReleaseDate(), "d-m-Y");

        return new NotificationDto($headline, $body);
    }
}