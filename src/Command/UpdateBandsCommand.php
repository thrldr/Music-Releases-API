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
            $latestAlbum = $this->musicDbService->getMostRecentAlbum($band);
            if ($this->albumIsNewLatest($latestAlbum, $band)) {
                $band->updateLatestRelease($latestAlbum);
            }
        }

        return Command::SUCCESS;
    }

    private function albumIsNewLatest(Album $album, Band $band)
    {
        return $album->getReleaseDate() > $band->getLastAlbum()->getReleaseDate();
    }
}