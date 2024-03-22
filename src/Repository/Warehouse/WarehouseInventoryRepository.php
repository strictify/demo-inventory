<?php

namespace App\Repository\Warehouse;

use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Warehouse\WarehouseInventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<WarehouseInventory>
 */
class WarehouseInventoryRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WarehouseInventory::class);
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
