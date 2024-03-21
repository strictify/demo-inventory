<?php

declare(strict_types=1);

namespace App\DTO\WebScraper;

use Money\Money;

class ProductDetailsDTO
{
    public function __construct(
        public string $name,
        public Money $price,
    )
    {
    }
}
