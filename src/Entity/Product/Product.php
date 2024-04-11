<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Override;
use Stringable;
use Money\Money;
use Money\Currency;
use App\Entity\IdTrait;
use App\Entity\Tax\Tax;
use App\Entity\User\User;
use App\Entity\ZohoAwareTrait;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\Category\Category;
use App\Entity\ZohoAwareInterface;
use App\Entity\TenantAwareInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use function array_values;

class Product implements TenantAwareInterface, ZohoAwareInterface, Stringable
{
    use IdTrait, TenantAwareTrait, ZohoAwareTrait;

    /**
     * @var Collection<array-key, ProductCategoryReference>
     */
    private Collection $categoryReferences;

    public function __construct(
        protected readonly Company $company,
        private string $name,
        private ?string $description = null,
        private ?Money $price = null,
        private ?Tax $tax = null,
    )
    {
        $this->categoryReferences = new ArrayCollection();
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    /**
     * @return list<Category>
     */
    public function getCategories(): array
    {
        $categories = $this->categoryReferences->map(fn(ProductCategoryReference $reference) => $reference->getCategory());

        return array_values($categories->toArray());
    }

    public function addCategory(Category $category, User $creator): void
    {
        if ($this->findReferenceToCategory($category)) {
            return;
        }

        $this->categoryReferences->add(new ProductCategoryReference(
            product: $this,
            category: $category,
            creator: $creator,
        ));
    }

    public function removeCategory(Category $category): void
    {
        if ($reference = $this->findReferenceToCategory($category)) {
            $this->categoryReferences->removeElement($reference);
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): Money
    {
        return $this->price ?: new Money(0, new Currency('USD'));
    }

    public function setPrice(?Money $price): void
    {
        $this->price = $price;
    }

    public function getTax(): ?Tax
    {
        return $this->tax;
    }

    public function setTax(?Tax $tax): void
    {
        $this->tax = $tax;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    private function findReferenceToCategory(Category $category): ?ProductCategoryReference
    {
        return $this->categoryReferences->findFirst(fn($_key, ProductCategoryReference $reference) => $reference->getCategory() === $category);
    }
}
