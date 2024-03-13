<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Money\Money;
use App\Entity\IdTrait;

class Product
{
    use IdTrait;

    public function __construct(
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
