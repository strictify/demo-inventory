<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Warehouse
{
    public function __construct(
        private string $warehouseId,
        private string $warehouseName,
    )
    {
    }

    public function getWarehouseId(): string
    {
        return $this->warehouseId;
    }

    public function getName(): string
    {
        return $this->warehouseName;
    }
}
