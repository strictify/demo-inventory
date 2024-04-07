<?php

declare(strict_types=1);

namespace App\Entity\Tax;

use Stringable;
use App\Entity\IdTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\TenantAwareInterface;
use function is_string;

class Tax implements TenantAwareInterface, Stringable
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        private readonly Company $company,
        private string $name,
        private float $value,
        private ?string $zohoId = null,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function setValue(float $value): void
    {
        $this->value = $value;
    }

    public function getZohoId(): ?string
    {
        $zohoId = $this->zohoId;

        return is_string($zohoId) ? $zohoId : null;
    }

    public function setZohoId(?string $zohoId): void
    {
        $this->zohoId = $zohoId;
    }
}
