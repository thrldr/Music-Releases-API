<?php

namespace App\Service\Notifier;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailNotifier implements NotifierInterface
{
    public function __construct(
        private MailerInterface $mailer,
        private string $from,
        private string $to,
        private string $subject,
    )
    {
    }

    function notify(mixed $payload)
    {
        $email = (new Email())
            ->from($this->from)
            ->to($this->to)
            ->subject($this->subject)
            ->text($payload);

        $this->mailer->send($email);
    }
}