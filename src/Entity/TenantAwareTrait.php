<?php

declare(strict_types=1);

namespace App\Entity;

use Override;
use App\Entity\Company\Company;

/**
 * @psalm-require-implements TenantAwareInterface
 */
trait TenantAwareTrait
{
    protected readonly Company $company;

    #[Override]
    public function getCompany(): Company
    {
        return $this->company;
    }
}
