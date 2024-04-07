<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use Generator;
use App\Entity\Company\Company;
use App\Entity\Warehouse\Warehouse;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class WarehouseFixtures extends Fixture implements DependentFixtureInterface
{
    public const string WAREHOUSE_1 = 'warehouse:1';

    public function __construct()
    {
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        foreach ($this->getData() as [$companyReference, $selfReference, $name]) {
            $company = $this->getReference($companyReference, Company::class);
            $warehouse = new Warehouse($company, $name);
            $this->setReference($selfReference, $warehouse);
            $manager->persist($warehouse);
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
     * @return Generator<array-key, list{CompanyFixtures::COMPANY_*, self::WAREHOUSE_*, string}>
     */
    private function getData(): Generator
    {
        yield [
            CompanyFixtures::COMPANY_1, self:: WAREHOUSE_1, 'wh1',
        ];
    }
}
