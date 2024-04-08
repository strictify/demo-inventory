<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Warehouse implements ZohoSingleResultInterface
{
    /**
     * @param non-empty-string|int $warehouseId
     */
    public function __construct(
        private string|int $warehouseId,
        private string $warehouseName,
    )
    {
    }

    /**
     * @return non-empty-string
     */
    public function getWarehouseId(): string
    {
        return (string)$this->warehouseId;
    }

    public function getName(): string
    {
        return $this->warehouseName;
    }

    public function getId(): string
    {
        return (string)$this->warehouseId;
    }
}
