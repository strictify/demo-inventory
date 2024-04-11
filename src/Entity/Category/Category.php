<?php

declare(strict_types=1);

namespace App\Entity\Category;

use Override;
use Stringable;
use App\Entity\IdTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\TenantAwareInterface;
use function is_string;

class Category implements TenantAwareInterface, Stringable
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        protected readonly Company $company,
        private string $name,
        private ?string $description,
    )
    {
    }

    #[Override]
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

    /**
     * @return non-empty-string|null
     */
    public function getDescription(): ?string
    {
        $description = $this->description;

        return is_string($description) && $description !== '' ? $description : null;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}
