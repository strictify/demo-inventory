<?php

declare(strict_types=1);

namespace App\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait IdTrait
{
    protected ?UuidInterface $id = null;

    public function getId(): string
    {
        $id = $this->id ??= $this->doCreateUuid();

        return $id->toString();
    }

    public function getUuid(): UuidInterface
    {
        return $this->id ??= $this->doCreateUuid();
    }

    /**
     * @internal
     * @psalm-internal App\Entity
     */
    private function doCreateUuid(): UuidInterface
    {
        return Uuid::uuid4();
    }
}
