<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\BandRepository;
use App\Repository\UserRepository;
use App\Service\Notification\NotifierLocator;
use App\Service\Notification\UserNotifiersParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: "app:notify",
    description: "The command notifies all users about new releases of the bands they are subscribed to"
)]
class NotificationCommand extends Command
{
    public function __construct(
        private BandRepository      $bandRepository,
        private UserRepository      $userRepository,
        private UserNotifiersParser $parser,
        private NotifierLocator     $notifierLocator,
    )
    {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int
    {
        try {
            $bands = $this->bandRepository->fetchAll();

            foreach ($bands as $band) {
                $subscribers = $band->getSubscribedUsers();

                /** @var User $subscriber */
                foreach ($subscribers as $subscriber) {
                    if (!$subscriber->isNotified()) {
                        $notifiers = $this->parser->parseNotifiers($subscriber);

                        foreach ($notifiers as $notifier) {
                            $notifier = $this->notifierLocator->get($notifier);
                            $notifier->notify($subscriber, $band->getLatestAlbum());
                        }

                        $subscriber->setIsNotified(true);
                        $this->userRepository->save($subscriber);
                    }
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }
}
