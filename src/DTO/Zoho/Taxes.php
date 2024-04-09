<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

use Override;
use LogicException;

/**
 * @implements ZohoMappingInterface<Tax>
 */
class Taxes implements ZohoMappingInterface
{
    /**
     * @param list<Tax> $taxes
     */
    public function __construct(
        public int $code,
        public string $message,
        private array $taxes,
        private ?Tax $tax = null,
    )
    {
    }

    #[Override]
    public function getOne(): object
    {
        return $this->tax ?? throw new LogicException();
    }

    #[Override]
    public function getMany(): array
    {
        return $this->taxes;
    }
}
