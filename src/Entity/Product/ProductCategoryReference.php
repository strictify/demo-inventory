<?php

declare(strict_types=1);

namespace App\Entity\Product;

use App\Entity\IdTrait;
use App\Entity\User\User;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\Category\Category;
use App\Entity\TenantAwareInterface;

class ProductCategoryReference implements TenantAwareInterface
{
    use IdTrait, TenantAwareTrait;

    /**
     * We need this field only for composite indexes.
     */
    protected readonly Company $company;

    public function __construct(
        private readonly Product $product,
        private readonly Category $category,
        private readonly User $creator,
        private ?string $comment = null,
    )
    {
        $this->company = $category->getCompany();
    }

    public function getCategory(): Category
    {
        return $this->category;
    }
}
