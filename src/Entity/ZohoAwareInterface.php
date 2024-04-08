<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Company\Company;

interface ZohoAwareInterface
{
    public function getCompany(): Company;

    /**
     * @return non-empty-string|null
     */
    public function getZohoId(): ?string;

    /**
     * @return non-empty-string
     */
    public function getId(): string;
}
