<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use function is_string;

class Item
{
    public function __construct(
        private string|int $itemId,
        private string $name,
        private ?string $description,
        private ?float $rate,
        private string|int|null $taxId,
    )
    {
    }

    public function getItemId(): string|int
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
     * @return non-empty-string|int|null
     */
    public function getTaxId(): string|int|null
    {
        $taxId = $this->taxId;
        if (is_string($taxId) && $taxId !== '') {
            return $taxId;
        }

        return null;
    }
}
