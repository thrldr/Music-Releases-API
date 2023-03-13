<?php

namespace App\Command;

use App\Entity\Album;
use App\Entity\Band;
use App\Repository\BandRepository;
use App\Service\MusicDb\MusicDbServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("app:update-bands")]
class UpdateBandsCommand extends Command
{
    public function __construct(
        private readonly BandRepository $bandRepository,
        private readonly MusicDbServiceInterface $musicDbService,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bands = $this->bandRepository->fetchAll();

        foreach ($bands as $band) {
            $latestAlbum = $this->musicDbService->getLatestAlbum($band->getName());
            if ($this->albumIsNewLatest($latestAlbum, $band)) {
                $output->writeln($band->getName() . " last album updated to " . $latestAlbum->getName());

                $band->updateLatestAlbum($latestAlbum);
                $this->bandRepository->save($band, true);
            }

            /** api requests throttling */
            sleep(2);
        }

        return Command::SUCCESS;
    }

    private function albumIsNewLatest(Album $album, Band $band)
    {
        return $band->getLatestAlbum() === null or $album->getName() !== $band->getLatestAlbum()->getName();
    }
}