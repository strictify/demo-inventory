<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use Override;

class Tax implements ZohoSingleResultInterface
{
    /**
     * @param non-empty-string|int $taxId
     */
    public function __construct(
        private string|int $taxId,
        private string $taxName,
        private float $taxPercentage,
    )
    {
    }

    /**
     * @return non-empty-string|int
     */
    public function getTaxId(): string|int
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

    #[Override]
    public function getId(): string
    {
        return (string)$this->getTaxId();
    }
}
