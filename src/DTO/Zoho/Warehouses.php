<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Warehouses
{
    /**
     * @param list<Warehouse> $warehouses
     */
    public function __construct(
        public int $code,
        public string $message,
        public array $warehouses,
    )
    {
    }
}
