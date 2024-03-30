<?php
declare(strict_types=1);

namespace App\Repository;

use Pagerfanta\Pagerfanta;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepository<T>
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    public function paginateWhere(int $page, array $_filters = []): Pagerfanta
    {
        $qb = $this->createQueryBuilder('o');
        if (!$qb->getDQLPart('orderBy')) {
            $this->setDefaultOrder($qb);
        }
        $adapter = new QueryAdapter($qb->getQuery());

        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($page);

        return $pagerfanta;
    }

    /**
     * @param T $entity
     */
    public function persist(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @param T $entity
     */
    public function remove(object $entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param T $entity
     */
    public function persistAndFlush(object $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    protected function setDefaultOrder(QueryBuilder $qb): void
    {
    }
}
