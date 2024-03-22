<?php

declare(strict_types=1);

namespace App\Entity\Warehouse;

use App\Entity\IdTrait;
use App\Entity\Product\Product;

class WarehouseInventory
{
    use IdTrait;

    public function __construct(
        private Warehouse $warehouse,
        private Product $product,
        private int $quantity,
    )
    {
    }

    public function getWarehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function setWarehouse(Warehouse $warehouse): void
    {
        $this->warehouse = $warehouse;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
