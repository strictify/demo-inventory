<?php

declare(strict_types=1);

namespace App\Entity;

interface ZohoAwareInterface
{
    /**
     * @return non-empty-string|null
     */
    public function getZohoId(): ?string;
}
