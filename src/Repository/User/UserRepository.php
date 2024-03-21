<?php

namespace App\Repository\User;

use App\Entity\User\User;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, private UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, User::class);
    }

    public function create(string $email, string $rawPassword): User
    {
        $user = new User(email: $email, password: '');
        $password = $this->passwordHasher->hashPassword($user, $rawPassword);
        $user->setPassword($password);
        $this->getEntityManager()->persist($user);

        return $user;
    }
}
