<?php

declare(strict_types=1);

namespace App\Entity\Product;

use Override;
use Stringable;
use Money\Money;
use Money\Currency;
use App\Entity\IdTrait;
use App\Entity\Tax\Tax;
use App\Entity\Company\Company;
use App\Entity\TenantAwareTrait;
use App\Entity\ZohoAwareInterface;
use App\Entity\TenantAwareInterface;
use function is_string;

class Product implements TenantAwareInterface, ZohoAwareInterface, Stringable
{
    use IdTrait, TenantAwareTrait;

    public function __construct(
        private readonly Company $company,
        private string $name,
        private ?string $description = null,
        private ?Money $price = null,
        private ?Tax $tax = null,
        private ?string $zohoId = null,
        private ZohoStatusEnum $zohoStatus = ZohoStatusEnum::NOT_CONNECTED,
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

    #[Override]
    /**
     * @return non-empty-string|null
     */
    public function getZohoId(): ?string
    {
        $zohoId = $this->zohoId;

        return is_string($zohoId) && $zohoId !== '' ? $zohoId : null;
    }

    public function getZohoStatus(): ZohoStatusEnum
    {
        return $this->zohoStatus;
    }

    public function setZohoStatus(ZohoStatusEnum $zohoStatus): void
    {
        $this->zohoStatus = $zohoStatus;
    }
}
