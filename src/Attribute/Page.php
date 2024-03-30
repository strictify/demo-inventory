<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;
use App\ValueResolver\PageValueResolver;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Page
{
    /**
     * @see PageValueResolver
     */
    public function __construct(
        public string $name = 'page',
    )
    {
    }
}
