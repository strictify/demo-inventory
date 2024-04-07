<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Tax
{
    public function __construct(
        private string $taxId,
        private string $taxName,
        private float $taxPercentage,
    )
    {
    }

    public function getTaxId(): string
    {
        return $this->taxId;
    }

    public function getTaxName(): string
    {
        return $this->taxName;
    }

    public function getTaxPercentage(): float
    {
        return $this->taxPercentage;
    }
}
