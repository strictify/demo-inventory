<?php

declare(strict_types=1);

namespace App\Entity\Vehicle;

use Override;
use Stringable;
use App\Entity\IdTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\TenantAwareInterface;

class Vehicle implements TenantAwareInterface, Stringable
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        protected readonly Company $company,
        private string $name,
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
}
