<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Company\Company;

interface TenantAwareInterface
{
    public function getCompany(): Company;
}
