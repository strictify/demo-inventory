<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Company\Company;
use App\Entity\Product\ZohoStatusEnum;

interface ZohoAwareInterface
{
    public function getCompany(): Company;

    /**
     * @return non-empty-string|null
     */
    public function getZohoId(): ?string;

    /**
     * @param non-empty-string|null $zohoId
     */
    public function setZohoId(?string $zohoId): void;

    /**
     * @return non-empty-string
     */
    public function getId(): string;

    public function getZohoStatus(): ZohoStatusEnum;

    public function setZohoStatus(ZohoStatusEnum $zohoStatus): void;
}
