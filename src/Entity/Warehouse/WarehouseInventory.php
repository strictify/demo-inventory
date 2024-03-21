<?php

declare(strict_types=1);

namespace App\Entity\Warehouse;

use App\Entity\IdTrait;
use App\Entity\Company\Company;
use App\Entity\Product\Product;
use App\Entity\TenantAwareTrait;
use App\Entity\TenantAwareInterface;

class WarehouseInventory implements TenantAwareInterface
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        private readonly Company $company,
        private Product $product,
        private int $quantity,
    )
    {
    }
}
