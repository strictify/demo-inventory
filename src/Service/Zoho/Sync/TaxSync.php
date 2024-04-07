<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use App\DTO\Zoho\Taxes;
use App\Entity\Tax\Tax;
use App\Entity\Company\Company;
use App\DTO\Zoho\Tax as ZohoTax;
use App\Service\Zoho\ZohoClient;
use App\Repository\Tax\TaxRepository;
use App\Service\Zoho\Sync\Model\SyncInterface;

/**
 * @implements SyncInterface<Tax>
 */
class TaxSync implements SyncInterface
{
    public function __construct(
        private ZohoClient $zohoClient,
        private TaxRepository $repository,
    )
    {
    }

    #[Override]
    public function getEntityName(): string
    {
        return Tax::class;
    }

    #[Override]
    public function onUpdate(object $entity, array $changeSet): iterable
    {
        return [];
    }

    #[Override]
    public function downloadAll(Company $company): void
    {
        $zohoTaxes = $this->zohoClient->get($company, '/settings/taxes', Taxes::class);
        foreach ($zohoTaxes->taxes as $zohoTax) {
            $tax = $this->getTax($zohoTax, $company);
            $tax->setName($zohoTax->getTaxName());
            $tax->setValue((int)$zohoTax->getTaxPercentage());
        }
        $this->repository->flush();
    }

    private function getTax(ZohoTax $zohoTax, Company $company): Tax
    {
        if ($tax = $this->repository->findOneBy(['zohoId' => $zohoTax->getTaxId()])) {
            return $tax;
        }
        $tax = new Tax($company, $zohoTax->getTaxName(), $zohoTax->getTaxPercentage(), zohoId: $zohoTax->getTaxId());
        $this->repository->persist($tax);

        return $tax;
    }
}
