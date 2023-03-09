<?php

namespace App\Service\Notification;

use App\Service\Notification\Notifier\EmailNotifier;
use App\Service\Notification\Notifier\NotifierInterface;
use App\Service\Notification\Notifier\TelegramNotifier;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class NotifierLocator implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator,
    )
    {
    }

    public function get(string $service): NotifierInterface
    {
        return $this->locator->get($service);
    }

    public static function getSubscribedServices(): array
    {
        return [
            EmailNotifier::class,
            TelegramNotifier::class,
        ];
    }
}