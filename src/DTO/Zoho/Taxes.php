<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Taxes
{
    /**
     * @param list<Tax> $taxes
     */
    public function __construct(
        public int $code,
        public string $message,
        public array $taxes,
    )
    {
    }
}
