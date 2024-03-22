<?php
declare(strict_types=1);

namespace App\DTO\Form;

use App\Entity\Warehouse\Warehouse;

class ProductInventoryDTO
{
    public function __construct(
        private Warehouse $warehouse,
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
