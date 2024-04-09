<?php

declare(strict_types=1);

namespace App\Entity\Warehouse;

use Override;
use Stringable;
use App\Entity\IdTrait;
use App\Entity\ZohoAwareTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\ZohoAwareInterface;
use App\Entity\TenantAwareInterface;
use function is_string;

class Warehouse implements TenantAwareInterface, Stringable, ZohoAwareInterface
{
    use IdTrait, TenantAwareTrait, ZohoAwareTrait;

    public function __construct(
        private readonly Company $company,
        private string $name,
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

    #[Override]
    public function getZohoId(): ?string
    {
        $zohoId = $this->zohoId;

        return is_string($zohoId) && $zohoId !== '' ? $zohoId : null;
    }
}
