<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use App\Entity\Company\Company;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class CompanyFixtures extends Fixture
{
    public const string COMPANY_1 = 'company:strictify';
    public const string COMPANY_2 = 'company:test_1';

    public function __construct()
    {
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $company1 = new Company('Strictify');
        $manager->persist($company1);
        $manager->persist($company1);
        $this->addReference(self::COMPANY_1, $company1);

        $company2 = new Company('Another company');
        $manager->persist($company2);
        $this->addReference(self::COMPANY_2, $company2);

        $manager->flush();
    }
}
