<?php

declare(strict_types=1);

namespace App\Entity\Company;

use Stringable;
use App\Entity\IdTrait;

class Company implements Stringable
{
    use IdTrait;

    public function __construct(
        private string $name,
    )
    {
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
