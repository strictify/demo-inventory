<?php

namespace App\Entity;

use Override;
use App\Entity\Product\ZohoStatusEnum;
use function is_string;

trait ZohoAwareTrait
{
    private ?string $zohoId = null;

    private ZohoStatusEnum $zohoStatus = ZohoStatusEnum::NOT_CONNECTED;

    /**
     * @return non-empty-string|null
     */
    #[Override]
    public function getZohoId(): ?string
    {
        $zohoId = $this->zohoId;

        return is_string($zohoId) && $zohoId !== '' ? $zohoId : null;
    }

    #[Override]
    public function setZohoId(?string $zohoId): void
    {
        $this->zohoId = $zohoId;
    }

    #[Override]
    public function getZohoStatus(): ZohoStatusEnum
    {
        return $this->zohoStatus;
    }

    #[Override]
    public function setZohoStatus(ZohoStatusEnum $zohoStatus): void
    {
        $this->zohoStatus = $zohoStatus;
    }

}
