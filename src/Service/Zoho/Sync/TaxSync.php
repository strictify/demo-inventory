<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use App\Entity\Tax\Tax;
use App\DTO\Zoho\Tax as ZohoTax;
use App\Entity\Company\Company;
use App\Entity\ZohoAwareInterface;
use App\DTO\Zoho\Taxes as ZohoTaxes;
use App\Repository\Tax\TaxRepository;
use App\Service\Zoho\Model\SyncInterface;
use App\Message\Zoho\ZohoSyncEntityMessage;
use function array_key_exists;

/**
 * @implements SyncInterface<Tax, ZohoTax, ZohoTaxes>
 */
class TaxSync implements SyncInterface
{
    public function __construct(
        private TaxRepository $taxRepository,
    )
    {
    }

    #[Override]
    public function map(ZohoAwareInterface $entity, object $mapping): void
    {
        $entity->setName($mapping->getTaxName());
        $entity->setValue((int)$mapping->getTaxPercentage());
    }

    #[Override]
    public function getBaseUrl(): string
    {
        return '/settings/taxes';
    }

    #[Override]
    public function findEntityByZohoId(string $zohoId): Tax|null
    {
        return $this->taxRepository->findOneBy(['zohoId' => $zohoId]);
    }

    #[Override]
    public function getMappingClass(): string
    {
        return ZohoTaxes::class;
    }

    #[Override]
    public function getEntityClass(): string
    {
        return Tax::class;
    }

    #[Override]
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable
    {
        if (!array_key_exists('name', $changeSet) && !array_key_exists('value', $changeSet)) {
            return;
        }

        yield new ZohoSyncEntityMessage($entity, 'put');
    }

    #[Override]
    public function createPutPayload(ZohoAwareInterface $entity): array
    {
        return [
            'tax_name' => $entity->getName(),
            'tax_percentage' => $entity->getValue(),
        ];
    }

    #[Override]
    public function createNewEntity(Company $company, object $mapping): Tax
    {
        $tax = new Tax($company, $mapping->getTaxName(), value: $mapping->getTaxPercentage());
        $this->taxRepository->persist($tax);

        return $tax;
    }
}
