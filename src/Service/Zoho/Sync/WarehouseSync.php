<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use App\DTO\Zoho\Warehouses;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\Entity\Warehouse\Warehouse;
use App\DTO\Zoho\Warehouse as ZohoWarehouse;
use App\Repository\Warehouse\WarehouseRepository;

class WarehouseSync
{
    public function __construct(
        private ZohoClient $zohoClient,
        private WarehouseRepository $warehouseRepository,
    )
    {
    }

    public function sync(Company $company): void
    {
        $warehouses = $this->zohoClient->get($company, '/settings/warehouses', Warehouses::class);
        foreach ($warehouses->warehouses as $zohoWarehouse) {
            $warehouse = $this->getWarehouse($zohoWarehouse, $company);
            $warehouse->setName($zohoWarehouse->getName());
        }
        $this->warehouseRepository->flush();
    }

    private function getWarehouse(ZohoWarehouse $zohoWarehouse, Company $company): Warehouse
    {
        if ($product = $this->warehouseRepository->findOneBy(['zohoId' => $zohoWarehouse->getWarehouseId()])) {
            return $product;
        }
        $warehouse = new Warehouse($company, $zohoWarehouse->getName(), zohoId: $zohoWarehouse->getWarehouseId());
        $this->warehouseRepository->persist($warehouse);

        return $warehouse;
    }
}
