<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use Override;
use LogicException;

/**
 * @implements ZohoMappingInterface<Warehouse>
 */
class Warehouses implements ZohoMappingInterface
{
    /**
     * @param list<Warehouse> $warehouses
     */
    public function __construct(
        public int $code,
        public string $message,
        private ?Warehouse $warehouse = null,
        public array $warehouses = [],
    )
    {
    }

    #[Override]
    public function getOne(): object
    {
        return $this->warehouse ?? throw new LogicException();
    }

    #[Override]
    public function getMany(): array
    {
        return $this->warehouses;
    }
}
