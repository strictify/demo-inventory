<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync;

use Money\Money;
use Money\Currency;
use App\DTO\Zoho\Items;
use App\Entity\Product\Product;
use App\Entity\Company\Company;
use App\Service\Zoho\ZohoClient;
use App\DTO\Zoho\Item as ZohoItem;
use App\Repository\Tax\TaxRepository;
use App\Repository\Product\ProductRepository;
use function is_float;
use function is_string;

class ProductSync
{
    public function __construct(
        private ZohoClient $zohoClient,
        private TaxRepository $repository,
        private ProductRepository $productRepository,
    )
    {
    }

    public function sync(Company $company): void
    {
        $zohoItems = $this->zohoClient->get($company, '/items', Items::class);
        foreach ($zohoItems->items as $zohoItem) {
            $product = $this->getProduct($zohoItem, $company);
            $rate = $zohoItem->getRate();
            $rate = is_float($rate) ? $rate : 0;
            $price = new Money((int)$rate * 100, new Currency('USD'));
            $product->setPrice($price);
            $product->setDescription($zohoItem->getDescription());
            if (is_string($zohoTaxId = $zohoItem->getTaxId())) {
                $tax = $this->repository->findOneBy(['zohoId' => $zohoTaxId]);
                $product->setTax($tax);
            } else {
                $product->setTax(null);
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
