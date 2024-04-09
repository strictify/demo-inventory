<?php

declare(strict_types=1);

namespace App\Message\Zoho;

use LogicException;
use App\Entity\ZohoAwareInterface;
use App\Message\AsyncMessageInterface;
use JetBrains\PhpStorm\ExpectedValues;
use function get_class;

/**
 * @psalm-type TAction = 'put'|'delete'|'get'
 */
class ZohoSyncEntityMessage implements AsyncMessageInterface
{
    /**
     * @var class-string<ZohoAwareInterface>
     */
    private string $class;

    /**
     * @var non-empty-string
     */
    private string $zohoId;

    /**
     * @param TAction $action
     */
    public function __construct(ZohoAwareInterface $entity, #[ExpectedValues(['put', 'delete', 'get'])] private string $action = 'put')
    {
        $this->class = get_class($entity);
        $this->zohoId = $entity->getZohoId() ?? throw new LogicException();
    }

    /**
     * @return class-string<ZohoAwareInterface>
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return non-empty-string
     */
    public function getZohoId(): string
    {
        return $this->zohoId;
    }

    /**
     * @return TAction
     */
    public function getAction(): string
    {
        return $this->action;
    }
}
