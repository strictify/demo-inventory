<?php

declare(strict_types=1);

namespace App\Message\Zoho;

use App\Entity\Company\Company;
use App\Message\AsyncMessageInterface;

class ZohoDownloadAllMessage implements AsyncMessageInterface
{
    /**
     * @var non-empty-string
     */
    private string $id;

    public function __construct(Company $company)
    {
        $this->id = $company->getId();
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
