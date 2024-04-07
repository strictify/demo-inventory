<?php

namespace App\Repository\Vehicle;

use App\Entity\Vehicle\Vehicle;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Vehicle>
 */
class VehicleRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vehicle::class);
    }
}
