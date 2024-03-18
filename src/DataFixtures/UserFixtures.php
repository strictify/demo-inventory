<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Persistence\ObjectManager;
use App\Repository\User\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User('admin@example.com', '', roles: ['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, '123123123');
        $user->setPassword($hashedPassword);
        $this->userRepository->persist($user);
        $manager->flush();
    }
}
