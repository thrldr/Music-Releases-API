<?php

namespace App\Command;

use App\Service\MusicDb\Discogs\AlbumData;
use App\Service\MusicDb\Discogs\DiscogsDb;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("test")]
class TestCommand extends Command
{

    public function __construct(
        private readonly DiscogsDb $db,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $bandApiId = $this->db->getBandServiceId("Gojira");
            $output->writeln(var_export($bandApiId, true));
//            /** @var AlbumData $latestAlbumData */
//            $latestAlbumData = $this->db->getLatestAlbum($bandApiId);
//            $output->writeln(var_export($latestAlbumData, true));
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage() . PHP_EOL);
            return Command::FAILURE;
        }
    }
}