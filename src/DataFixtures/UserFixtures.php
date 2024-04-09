<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Override;
use Generator;
use App\Entity\User\User;
use App\Entity\Company\Company;
use Doctrine\Persistence\ObjectManager;
use App\Repository\User\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    #[Override]
    public function load(ObjectManager $manager): void
    {
        $admin = new User(email: 'admin@example.com', password: '', firstName: 'Super', lastName: 'Admin', roles: ['ROLE_ADMIN', 'ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, '123123123');
        $admin->setPassword($hashedPassword);
        $this->userRepository->persist($admin);

        foreach ($this->getData() as [$companyReference, $email, $password, $firstName, $lastName]) {
            $company = $this->getReference($companyReference, Company::class);
            $user = new User(email: $email, password: '', firstName: $firstName, lastName: $lastName, company: $company, roles: ['ROLE_COMPANY', 'ROLE_USER']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
            $this->userRepository->persist($user);
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
     * @return Generator<array-key, list{CompanyFixtures::COMPANY_*, string, string, string, string}>
     */
    private function getData(): Generator
    {
        yield [
            CompanyFixtures::COMPANY_1, 'demo@example.com', 'demo', 'Demo', 'Demo',
        ];
    }
}
