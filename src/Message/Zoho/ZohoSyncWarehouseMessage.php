<?php

declare(strict_types=1);

namespace App\Message\Zoho;

use App\Entity\Warehouse\Warehouse;
use App\Message\AsyncMessageInterface;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @psalm-type TAction = 'update'|'remove'
 */
class ZohoSyncWarehouseMessage implements AsyncMessageInterface
{
    private string $id;

    /**
     * @param TAction $action
     */
    public function __construct(Warehouse $warehouse, #[ExpectedValues(['update', 'remove'])] private string $action)
    {
        $this->id = $warehouse->getId();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return TAction
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
