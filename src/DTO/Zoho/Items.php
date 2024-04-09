<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use Override;
use LogicException;

/**
 * @implements ZohoMappingInterface<Item>
 */
class Items implements ZohoMappingInterface
{
    /**
     * @param list<Item> $items
     */
    public function __construct(
        public int $code,
        public string $message,
        public ?Item $item = null,
        public array $items = [],
    )
    {
    }

    #[Override]
    public function getOne(): object
    {
        return $this->item ?? throw new LogicException();
    }

    #[Override]
    public function getMany(): array
    {
        return $this->items;
    }
}
