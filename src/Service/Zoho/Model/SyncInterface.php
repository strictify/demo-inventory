<?php

declare(strict_types=1);

namespace App\Service\Zoho\Model;

use App\Entity\Company\Company;
use App\Entity\ZohoAwareInterface;
use App\DTO\Zoho\ZohoMappingInterface;
use App\Message\AsyncMessageInterface;
use App\DTO\Zoho\ZohoSingleResultInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @template TEntity of ZohoAwareInterface
 * @template TK of ZohoSingleResultInterface
 * @template TMapping of ZohoMappingInterface<TK>
 */
#[AutoconfigureTag(name: self::class)]
interface SyncInterface
{
    /**
     * @param non-empty-string $zohoId
     *
     * @return TEntity|null
     */
    public function findEntityByZohoId(string $zohoId): ZohoAwareInterface|null;

    /**
     * @param TEntity $entity
     *
     * @return non-empty-array<string, scalar>
     */
    public function createPutPayload(ZohoAwareInterface $entity): array;

    /**
     * @return class-string<TEntity>
     */
    public function getEntityClass(): string;

    /**
     * @return class-string<TMapping>
     */
    public function getMappingClass(): string;

    /**
     * @return non-empty-string
     */
    public function getBaseUrl(): string;

    /**
     * When an entity from @see getEntityName is updated, return iterable of messages to be executed after succesful flush.
     *
     * @no-named-arguments
     *
     * @param TEntity $entity
     *
     * @return iterable<AsyncMessageInterface>
     */
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable;

    /**
     * @param TEntity $entity
     * @param TK $mapping
     */
    public function map(ZohoAwareInterface $entity, object $mapping): void;

    /**
     * @param TK $mapping
     *
     * @return TEntity
     */
    public function createNewEntity(Company $company, object $mapping): ZohoAwareInterface;
}
