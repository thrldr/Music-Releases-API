<?php

namespace App\Service\Notification\Notifier;

use App\Entity\Album;
use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifier implements NotifierInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $from,
        private string $subject,
    )
    {
    }

    function notify(User $user, Album $newAlbum): void
    {
        $email = (new Email())
            ->from($this->from)
            ->to($user->getEmail())
            ->subject($this->subject)
            ->text($newAlbum->getName());

        $this->mailer->send($email);
    }
}