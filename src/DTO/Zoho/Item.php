<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use Override;
use function is_string;

class Item implements ZohoSingleResultInterface
{
    /**
     * @param non-empty-string|int $itemId
     */
    public function __construct(
        private string|int $itemId,
        private string $name,
        private ?string $description,
        private ?float $rate,
        private string|int|null $taxId,
    )
    {
    }

    /**
     * @return non-empty-string|int
     */
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

    #[Override]
    public function getId(): string
    {
        return (string)$this->getItemId();
    }
}
