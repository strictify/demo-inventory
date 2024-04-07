<?php

declare(strict_types=1);

namespace App\Service\Zoho\Sync\Model;

use App\Entity\Company\Company;
use App\Message\AsyncMessageInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * @template T of object
 */
#[AutoconfigureTag(name: self::class)]
interface SyncInterface
{
    /**
     * @return class-string<T>
     */
    public function getEntityName(): string;

    /**
     * @param T $entity
     *
     * @return iterable<AsyncMessageInterface>
     */
    public function onUpdate(object $entity, array $changeSet): iterable;

    public function downloadAll(Company $company): void;
}
