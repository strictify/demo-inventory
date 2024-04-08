<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use App\DTO\Zoho\Warehouses;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\Entity\ZohoAwareInterface;
use App\Entity\Warehouse\Warehouse;
use App\DTO\Zoho\Warehouse as ZohoWarehouse;
use App\Service\Zoho\Sync\Model\SyncInterface;
use App\Message\Zoho\ZohoSyncWarehouseMessage;
use App\Repository\Warehouse\WarehouseRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function is_string;
use function array_key_exists;

/**
 * @implements SyncInterface<Warehouse>
 */
class WarehouseSync implements SyncInterface
{
    public function __construct(
        private ZohoClient $zohoClient,
        private WarehouseRepository $warehouseRepository,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoSyncWarehouseMessage $message): void
    {
        if (!$warehouse = $this->warehouseRepository->find($message->getId())) {
            return;
        }
        if (!is_string($zohoId = $warehouse->getZohoId())) {
            return;
        }
        // @see https://www.zoho.com/inventory/api/v1/multi-warehouse/#overview
        $url = sprintf('/settings/warehouses/%s', $zohoId);

        match ($message->getAction()) {
            'remove' => $this->zohoClient->delete($warehouse->getCompany(), $url),
            'update' => $this->zohoClient->put($warehouse->getCompany(), $url, data: [
                'warehouse_name' => $warehouse->getName(),
            ]),
        };
    }

    #[Override]
    public function getEntityName(): string
    {
        return Warehouse::class;
    }

    #[Override]
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable
    {
        if (!array_key_exists('name', $changeSet)) {
            return;
        }

        yield new ZohoSyncWarehouseMessage($entity, 'update');
    }

    #[Override]
    public function downloadAll(Company $company): void
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
