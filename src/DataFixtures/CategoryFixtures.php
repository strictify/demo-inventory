<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use Generator;
use App\Entity\Company\Company;
use App\Entity\Category\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CategoryFixtures extends Fixture implements DependentFixtureInterface
{
    public const string CATEGORY_1 = 'category:1';

    #[Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$companyReference, $selfReference, $name, $description]) {
            $company = $this->getReference($companyReference, Company::class);
            $category = new Category($company, $name, $description);
            $this->setReference($selfReference, $category);
            $manager->persist($category);
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
     * @return Generator<array-key, list{CompanyFixtures::COMPANY_*, self::CATEGORY_*, string, string}>
     */
    private function getData(): Generator
    {
        yield [
            CompanyFixtures::COMPANY_1, self::CATEGORY_1, 'Category name', 'Category description',
        ];
    }
}
