<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use App\Repository\User\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = $this->userRepository->create('admin@example.com', '123123123');
        $manager->flush();
    }
}
