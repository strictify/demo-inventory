<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use App\DTO\Zoho\Taxes;
use App\Entity\Tax\Tax;
use App\Entity\Company\Company;
use App\DTO\Zoho\Tax as ZohoTax;
use App\Service\Zoho\ZohoClient;
use App\Repository\Tax\TaxRepository;

class TaxSync
{
    public function __construct(
        private ZohoClient $zohoClient,
        private TaxRepository $repository,
    )
    {
    }

    public function sync(Company $company): void
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
