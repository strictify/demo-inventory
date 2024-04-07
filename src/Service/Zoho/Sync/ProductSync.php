<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Override;
use Money\Money;
use Money\Currency;
use App\DTO\Zoho\Items;
use InvalidArgumentException;
use App\Entity\Product\Product;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\DTO\Zoho\Item as ZohoItem;
use App\Repository\Tax\TaxRepository;
use App\Message\Zoho\ZohoSyncProductMessage;
use App\Repository\Product\ProductRepository;
use App\Service\Zoho\Sync\Model\SyncInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use function is_float;
use function is_string;
use function strip_tags;
use function array_key_exists;

/**
 * @implements SyncInterface<Product>
 */
class ProductSync implements SyncInterface
{
    public function __construct(
        private ZohoClient $zohoClient,
        private TaxRepository $repository,
        private ProductRepository $productRepository,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ZohoSyncProductMessage $message): void
    {
        try {
            if (!$product = $this->productRepository->find($message->getId())) {
                return;
            }
            if (!is_string($zohoId = $product->getZohoId())) {
                return;
            }
            $url = sprintf('/items/%s', $zohoId);

            match ($message->getAction()) {
                'update' => $this->zohoClient->put($product->getCompany(), $url, ['name' => $product->getName(), 'description' => strip_tags($product->getDescription() ?? '')]),
                'remove' => $this->zohoClient->delete($product->getCompany(), $url),
            };
        } catch (InvalidArgumentException $e) {
            throw new UnrecoverableMessageHandlingException(previous: $e);
        }
    }

    #[Override]
    public function getEntityName(): string
    {
        return Product::class;
    }

    #[Override]
    public function onUpdate(object $entity, array $changeSet): iterable
    {
        if (!array_key_exists('name', $changeSet) && !array_key_exists('description', $changeSet)) {
            return;
        }

        yield new ZohoSyncProductMessage($entity, 'update');
    }

    #[Override]
    public function downloadAll(Company $company): void
    {
        $zohoItems = $this->zohoClient->get($company, '/items', Items::class);
        foreach ($zohoItems->items as $zohoItem) {
            $product = $this->getProduct($zohoItem, $company);
            $rate = $zohoItem->getRate();
            $rate = is_float($rate) ? $rate : 0;
            $price = new Money((int)$rate * 100, new Currency('USD'));
            $product->setPrice($price);

            $product->setName($zohoItem->getName());
            $product->setDescription($zohoItem->getDescription());

            if (!is_string($zohoTaxId = $zohoItem->getTaxId())) {
                $product->setTax(null);
            } else {
                $tax = $this->repository->findOneBy(['zohoId' => $zohoTaxId]);
                $product->setTax($tax);
            }
        }
        $this->productRepository->flush();
    }

    private function getProduct(ZohoItem $zohoItem, Company $company): Product
    {
        if ($product = $this->productRepository->findOneBy(['zohoId' => $zohoItem->getItemId()])) {
            return $product;
        }
        $product = new Product($company, name: $zohoItem->getName(), zohoId: $zohoItem->getItemId());
        $this->productRepository->persist($product);

        return $product;
    }
}
