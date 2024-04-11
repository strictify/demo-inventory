<?php

declare(strict_types=1);

namespace App\Entity\Tax;

use Override;
use Stringable;
use App\Entity\IdTrait;
use App\Entity\ZohoAwareTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\ZohoAwareInterface;
use App\Entity\TenantAwareInterface;

class Tax implements TenantAwareInterface, Stringable, ZohoAwareInterface
{
    use IdTrait, TenantAwareTrait, ZohoAwareTrait;

    public function __construct(
        protected readonly Company $company,
        private string $name,
        private float $value,
    )
    {
    }

    #[Override]
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
}
