<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Company\Company;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Repository\Company\CompanyRepository;

class CompanyFixtures extends Fixture
{
    public function __construct(
        private CompanyRepository $companyRepository,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $company1 = new Company('Strictify');
        $this->companyRepository->persist($company1);
        $company2 = new Company('Another company');
        $this->companyRepository->persist($company2);
        $manager->flush();
    }
}
