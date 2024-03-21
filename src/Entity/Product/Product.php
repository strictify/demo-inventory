<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Money\Money;
use App\Entity\IdTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\TenantAwareInterface;

class Product implements TenantAwareInterface
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        private readonly Company $company,
        private string $name,
        private ?Money $price = null,
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): ?Money
    {
        return $this->price;
    }

    public function setPrice(?Money $price): void
    {
        $this->price = $price;
    }
}
