<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

interface ZohoSingleResultInterface
{
    /**
     * @return non-empty-string
     */
    public function getId(): string;
}
