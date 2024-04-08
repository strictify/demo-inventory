<?php

declare(strict_types=1);

namespace App\Command;

use LogicException;
use App\Service\Zoho\ZohoManager;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @psalm-suppress all
 */
#[AsCommand(
    name: 'dummy',
    description: 'Add a short description for your command',
)]
class DummyCommand extends Command
{
    public function __construct(
        private ZohoManager $zohoSync,
        private CompanyRepository $companyRepository,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $company = $this->companyRepository->findOneBy(['name' => 'Strictify']) ?? throw new LogicException();
        $this->zohoSync->downloadAll($company);

        return Command::SUCCESS;
    }
}
