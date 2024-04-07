<?php

declare(strict_types=1);

namespace App\Message;

use App\Entity\Company\Company;

class ZohoSyncMessage implements AsyncMessageInterface
{
    private string $id;

    public function __construct(Company $company)
    {
        $this->id = $company->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }
}
