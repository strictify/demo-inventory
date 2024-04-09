<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use Money\Money;
use Money\Currency;
use App\Entity\Tax\Tax;
use App\Entity\Product\Product;
use App\Entity\Company\Company;
use App\DTO\Zoho\Item as ZohoItem;
use App\Entity\ZohoAwareInterface;
use App\DTO\Zoho\Items as ZohoItems;
use App\Repository\Tax\TaxRepository;
use App\Entity\Product\ZohoStatusEnum;
use App\Service\Zoho\Model\SyncInterface;
use App\Message\Zoho\ZohoSyncEntityMessage;
use App\Repository\Product\ProductRepository;
use function is_float;
use function is_string;
use function strip_tags;
use function any_key_exists;

/**
 * @implements SyncInterface<Product, ZohoItem, ZohoItems>
 */
class ProductSync implements SyncInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private TaxRepository $taxRepository,
    )
    {
    }

    #[Override]
    public function map(ZohoAwareInterface $entity, object $mapping): void
    {
        $rate = $mapping->getRate();
        $rate = is_float($rate) ? $rate : 0;
        $price = new Money((int)($rate * 100), new Currency('USD'));
        $entity->setPrice($price);

        $entity->setName($mapping->getName());
        $entity->setDescription($mapping->getDescription());

        $tax = $this->findTax($mapping);
        $entity->setTax($tax);
    }

    #[Override]
    public function getBaseUrl(): string
    {
        return '/items';
    }

    #[Override]
    public function findEntityByZohoId(string $zohoId): Product|null
    {
        return $this->productRepository->findOneBy(['zohoId' => $zohoId]);
    }

    #[Override]
    public function getMappingClass(): string
    {
        return ZohoItems::class;
    }

    #[Override]
    public function getEntityClass(): string
    {
        return Product::class;
    }

    #[Override]
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable
    {
        if (!any_key_exists(['name', 'description', 'price', 'tax'], $changeSet)) {
            return;
        }
        $entity->setZohoStatus(ZohoStatusEnum::BUSY);

        yield new ZohoSyncEntityMessage($entity, 'put');
    }

    #[Override]
    public function createPutPayload(ZohoAwareInterface $entity): array
    {
        return [
            'rate' => (float)($entity->getPrice()->getAmount()) / 100,
            'name' => $entity->getName(),
            'description' => strip_tags($entity->getDescription() ?? ''),
        ];
    }

    #[Override]
    public function createNewEntity(Company $company, object $mapping): Product
    {
        $product = new Product($company, $mapping->getName());
        $this->productRepository->persist($product);

        return $product;
    }

    private function findTax(ZohoItem $zohoItem): ?Tax
    {
        if (!is_string($zohoTaxId = $zohoItem->getTaxId())) {
            return null;
        }

        return $this->taxRepository->findOneBy(['zohoId' => $zohoTaxId]);
    }
}
