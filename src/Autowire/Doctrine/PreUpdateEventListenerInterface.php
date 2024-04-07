<?php

declare(strict_types=1);

namespace App\Autowire\Doctrine;

use Doctrine\ORM\Event\PreUpdateEventArgs;

interface PreUpdateEventListenerInterface
{
    /**
     * @api
     */
    public function preUpdate(PreUpdateEventArgs $event): void;
}
