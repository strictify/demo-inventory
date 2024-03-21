<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Company\Company;

/**
 * @psalm-require-implements TenantAwareInterface
 */
trait TenantAwareTrait
{
    private readonly Company $company;

    public function getCompany(): Company
    {
        return $this->company;
    }
}
