<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use Generator;
use App\Entity\Product\Product;
use App\Entity\Warehouse\Warehouse;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Warehouse\WarehouseInventory;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class InventoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$warehouseReference, $productReference, $quantity]) {
            $warehouse = $this->getReference($warehouseReference, Warehouse::class);
            $product = $this->getReference($productReference, Product::class);
            $inventory = new WarehouseInventory($warehouse, $product, $quantity);
            $manager->persist($inventory);
        }
        $manager->flush();
    }

    #[Override]
    public function getDependencies(): array
    {
        return [
            WarehouseFixtures::class,
        ];
    }

    /**
     * @return Generator<array-key, list{WarehouseFixtures::WAREHOUSE_*, ProductFixtures::PRODUCT_*, int}>
     */
    private function getData(): Generator
    {
        yield [
            WarehouseFixtures::WAREHOUSE_1, ProductFixtures::PRODUCT_1, 100,
        ];
    }
}
