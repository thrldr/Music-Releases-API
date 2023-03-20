<?php

namespace App\Command;

use App\Repository\AlbumRepository;
use App\Repository\BandRepository;
use App\Service\MusicDb\Discogs\AlbumData;
use App\Service\MusicDb\Discogs\DiscogsDbRemote;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("test")]
class TestCommand extends Command
{

    public function __construct(
        private readonly BandRepository  $bandRepository,
        private readonly AlbumRepository $albumRepository,
        private readonly DiscogsDbRemote $db,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $bands = $this->bandRepository->fetchAll();
        foreach ($bands as $band) {
            $album = $band->getLatestAlbum();
            if (isset($album)) {
                $this->albumRepository->remove($band->getLatestAlbum());
            }
            $band->setLatestAlbum(null);
            $this->bandRepository->save($band, true);
        }

        $output->writeln("OK");
        return Command::SUCCESS;

//        try {
//            $latestAlbum = $this->db->getLatestAlbum("Miles Davis");
//            $output->writeln(var_export($latestAlbum, true));
//            return Command::SUCCESS;
//        } catch (\Exception $exception) {
//            $output->writeln($exception->getMessage() . PHP_EOL);
//            return Command::FAILURE;
//        }
    }
}