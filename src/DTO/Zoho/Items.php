<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

class Items
{
    /**
     * @param list<Item> $items
     */
    public function __construct(
        public int $code,
        public string $message,
        public array $items,
    )
    {
    }
}
