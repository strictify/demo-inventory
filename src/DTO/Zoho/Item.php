<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use function is_string;

class Item
{
    public function __construct(
        private string $itemId,
        private string $name,
        private ?string $description,
        private ?float $rate,
        private ?string $taxId,
    )
    {
    }

    public function getItemId(): string
    {
        return $this->itemId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    /**
     * @return non-empty-string|null
     */
    public function getTaxId(): ?string
    {
        $taxId = $this->taxId;
        if (is_string($taxId) && $taxId !== '') {
            return $taxId;
        }

        return null;
    }
}
