<?php

declare(strict_types=1);

namespace App\Autowire\Doctrine;

interface PostFlushEventListenerInterface
{
    /**
     * @api
     */
    public function postFlush(): void;
}
