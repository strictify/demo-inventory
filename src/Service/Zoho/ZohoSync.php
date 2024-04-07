<?php

declare(strict_types=1);

namespace App\Service\Zoho;

use App\Entity\Company\Company;
use App\Message\ZohoSyncMessage;
use App\Service\Zoho\Sync\TaxSync;
use App\Service\Zoho\Sync\ProductSync;
use App\Service\Zoho\Sync\WarehouseSync;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use function sprintf;

class ZohoSync
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private TaxSync $taxSync,
        private ProductSync $productSync,
        private WarehouseSync $warehouseSync,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoSyncMessage $message): void
    {
        $id = $message->getId();
        $company = $this->companyRepository->find($id) ?? throw new UnrecoverableMessageHandlingException(sprintf('Company %s does not exist.', $id));
        $this->sync($company);
    }

    public function sync(Company $company): void
    {
        $this->taxSync->sync($company);
        $this->productSync->sync($company);
        $this->warehouseSync->sync($company);
    }
}
