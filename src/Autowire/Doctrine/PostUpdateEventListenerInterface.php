<?php

declare(strict_types=1);

namespace App\Autowire\Doctrine;

use Doctrine\ORM\Event\PostUpdateEventArgs;

interface PostUpdateEventListenerInterface
{
    /**
     * @api
     */
    public function postUpdate(PostUpdateEventArgs $event): void;
}
