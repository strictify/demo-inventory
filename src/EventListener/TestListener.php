<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Product\Product;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TestListener
{
    /**
     * psalm-suppress UnusedVariable
     *
     * param PreUpdateEventArgs<Product> $event
     */
    public function update(PreUpdateEventArgs $event): void
    {
        $entity = $event->getObject();
        if (!$entity instanceof Product) {
            return;
        }
//        $changeSet = $event->getEntityChangeSet();
//        /** @psalm-trace $changeSet */

        $_a = $event->getOldValue('company');
        /** @psalm-trace $_a */
    }
}
