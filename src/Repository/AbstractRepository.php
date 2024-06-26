<?php
declare(strict_types=1);

namespace App\Repository;

use Pagerfanta\Pagerfanta;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;

/**
 * @template T of object
 *
 * @extends ServiceEntityRepositoryProxy<T>
 *
 * @psalm-suppress InternalMethod
 * @psalm-suppress InternalClass
 */
abstract class AbstractRepository extends ServiceEntityRepositoryProxy
{
    /**
     * @psalm-suppress MixedReturnTypeCoercion - Doctrine needs to be psalmified first
     *
     * @return Pagerfanta<T>
     */
    public function paginateWhere(int $page, array $_filters = []): Pagerfanta
    {
        $qb = $this->createQueryBuilder('o');
        $this->setDefaultOrder($qb);
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
        $qb->orderBy('o.id');
    }
}
