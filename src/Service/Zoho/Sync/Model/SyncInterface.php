<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync\Model;

use App\Entity\Company\Company;
use App\Entity\ZohoAwareInterface;
use App\Message\AsyncMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @template T of ZohoAwareInterface
 */
#[AutoconfigureTag(name: self::class)]
interface SyncInterface
{
    /**
     * @return class-string<T>
     */
    public function getEntityName(): string;

    /**
     * When an entity from @see getEntityName is updated, return iterable of messages to be executed after succesful flush.
     *
     * @no-named-arguments
     *
     * @param T $entity
     *
     * @return iterable<AsyncMessageInterface>
     */
    public function onUpdate(ZohoAwareInterface $entity, array $changeSet): iterable;

    public function downloadAll(Company $company): void;
}
