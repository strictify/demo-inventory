<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Command;

use LogicException;
use App\Service\ZohoImprovedManager;
use App\Service\Mercure\StreamBuilder;
use Symfony\Component\Mercure\HubInterface;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @psalm-suppress all
 */
#[AsCommand(
    name: 'dummy',
    description: 'Add a short description for your command',
)]
class BigDataPopulateCommand extends Command
{
    public function __construct(
        private ZohoImprovedManager $zohoImprovedManager,
        private CompanyRepository $companyRepository,
        private HubInterface $hub,
        private StreamBuilder $streamBuilder,
        private MessageBusInterface $messageBus,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $company = $this->companyRepository->findOneBy(['name' => 'Strictify']) ?? throw new LogicException();

        return Command::SUCCESS;
    }
}
