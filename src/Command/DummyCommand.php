<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Panther\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'dummy',
    description: 'Add a short description for your command',
)]
class DummyCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $client = Client::createChromeClient();
        $crawler = $client->request('GET', 'https://example.com');

//        dump($crawler->getText());

        return Command::SUCCESS;
    }
}
