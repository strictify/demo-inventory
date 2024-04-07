<?php

declare(strict_types=1);

namespace App\Message\Zoho;

use App\Entity\Product\Product;
use App\Message\AsyncMessageInterface;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * @psalm-type TAction = 'update'|'remove'
 */
class ZohoSyncProductMessage implements AsyncMessageInterface
{
    private string $id;

    /**
     * @param TAction $action
     */
    public function __construct(Product $product, #[ExpectedValues(['update', 'remove'])] private string $action)
    {
        $this->id = $product->getId();
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
