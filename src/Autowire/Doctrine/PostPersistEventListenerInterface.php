<?php

declare(strict_types=1);

namespace App\Autowire\Doctrine;

use Doctrine\ORM\Event\PostPersistEventArgs;

interface PostPersistEventListenerInterface
{
    /**
     * @api
     */
    public function postPersist(PostPersistEventArgs $event): void;
}
