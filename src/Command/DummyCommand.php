<?php /** @noinspection PhpPropertyOnlyWrittenInspection */
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Command;

use LogicException;
use App\Turbo\Stream\ReloadStream;
use App\Service\ZohoImprovedManager;
use App\Service\Mercure\StreamBuilder;
use Symfony\Component\Mercure\HubInterface;
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
        private ZohoImprovedManager $zohoImprovedManager,
        private CompanyRepository $companyRepository,
        private HubInterface $hub,
        private StreamBuilder $streamBuilder,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $company = $this->companyRepository->findOneBy(['name' => 'Strictify']) ?? throw new LogicException();

        $this->streamBuilder->pushToApp(
            $company,
            new ReloadStream('product-1c5fb58e-a2eb-451d-b95f-ad0149ea3837'),
        );

//        $company = $this->companyRepository->findOneBy(['name' => 'Strictify']) ?? throw new LogicException();

//        $this->zohoImprovedManager->downloadAll($company);

        return Command::SUCCESS;
    }
}
