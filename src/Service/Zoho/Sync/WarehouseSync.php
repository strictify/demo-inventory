<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use App\Entity\ZohoAwareInterface;
use App\Entity\Warehouse\Warehouse;
use App\Message\Zoho\ZohoPutEntityMessage;
use App\DTO\Zoho\Warehouse as ZohoWarehouse;
use App\DTO\Zoho\Warehouses as ZohoWarehouses;
use App\Repository\Warehouse\WarehouseRepository;
use App\Service\Zoho\Model\SyncInterface;
use function array_key_exists;

/**
 * @implements SyncInterface<Warehouse, ZohoWarehouse, ZohoWarehouses>
 */
class WarehouseSync implements SyncInterface
{
    public function __construct(
        private WarehouseRepository $warehouseRepository,
    )
    {
    }

    #[Override]
    public function map(ZohoAwareInterface $entity, object $mapping): void
    {
        $entity->setName($mapping->getName());
    }

    #[Override]
    public function getBaseUrl(): string
    {
        return '/settings/warehouses';
    }

    #[Override]
    public function findEntityByZohoId(string $zohoId): Warehouse|null
    {
        return $this->warehouseRepository->findOneBy(['zohoId' => $zohoId]);
    }

    #[Override]
    public function getMappingClass(): string
    {
        return ZohoWarehouses::class;
    }

    #[Override]
    public function getEntityClass(): string
    {
        return Warehouse::class;
    }

    #[Override]
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable
    {
        if (!array_key_exists('name', $changeSet) && !array_key_exists('value', $changeSet)) {
            return;
        }

        yield new ZohoPutEntityMessage($entity, 'put');
    }

    #[Override]
    public function createPutPayload(ZohoAwareInterface $entity): array
    {
        return [
            'warehouse_name' => $entity->getName(),
        ];
    }
}
