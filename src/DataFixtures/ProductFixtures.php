<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use Generator;
use Money\Money;
use Money\Currency;
use App\Entity\Company\Company;
use App\Entity\Product\Product;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public const string PRODUCT_1 = 'product:1';

    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$companyReference, $selfReference, $name, $price]) {
            $company = $this->getReference($companyReference, Company::class);
            $product = new Product($company, $name, price: $price);
            $this->setReference($selfReference, $product);
            $manager->persist($product);
        }
        $manager->flush();
    }

    #[Override]
    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }

    /**
     * @return Generator<array-key, list{CompanyFixtures::COMPANY_*, self::PRODUCT_*, string, Money}>
     */
    private function getData(): Generator
    {
        yield [
            CompanyFixtures::COMPANY_1, self::PRODUCT_1, 'Product 1', new Money(1234, new Currency('USD')),
        ];
    }
}
