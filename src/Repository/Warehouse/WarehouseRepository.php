<?php

namespace App\Repository\Warehouse;

use App\Entity\Warehouse\Warehouse;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Warehouse>
 */
class WarehouseRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Warehouse::class);
    }

//    public function create(string $email, string $rawPassword): Company
//    {
//        $user = new User(email: $email, password: '');
//        $password = $this->passwordHasher->hashPassword($user, $rawPassword);
//        $user->setPassword($password);
//        $this->getEntityManager()->persist($user);
//
//        return $user;
//    }
}
