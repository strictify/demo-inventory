<?php

namespace App\Repository\Company;

use App\Entity\Company\Company;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Company>
 */
class CompanyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
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
