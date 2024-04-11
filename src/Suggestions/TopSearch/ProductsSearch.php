<?php

declare(strict_types=1);

namespace App\Suggestions\TopSearch;

use Override;
use Generator;
use App\Entity\Product\Product;
use App\Entity\Category\ProductCategory;
use App\Entity\Category\BiomarkerCategory;
use App\Entity\Product\ProductTestLabEnum;
use App\Repository\Product\ProductRepository;
use Symfony\Component\Routing\RouterInterface;
use App\Repository\Category\ProductCategoryRepository;
use App\Repository\Category\BiomarkerCategoryRepository;
use function iterable_map;

class ProductsSearch implements TopSearchSuggestionsInterface
{
    public function __construct(
        private ProductRepository $repository,
    )
    {
    }

    #[Override]
    public function getGroupName(): string
    {
        return 'Products';
    }

    #[Override]
    public function getResults(string $q, RouterInterface $router): Generator
    {
        $products = $this->repository->findAll();

        yield from iterable_map($products, fn(Product $product) => $this->generateResultStruct($product, $router));
    }

    /**
     * @return array{url: string, name: string}
     */
    private function generateResultStruct(Product $product, RouterInterface $router): array
    {
        return [
            'url' => $router->generate('app_products_update', ['id' => $product->getId()]),
            'name' => $product->getName(),
        ];
    }
}
