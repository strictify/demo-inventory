<?php

declare(strict_types=1);

namespace App\Entity\Product;

enum ZohoStatusEnum: string
{
    case NOT_CONNECTED = 'not_connected';
    case BUSY = 'busy';
    case SYNCED = 'synced';

    public function isBusy(): bool
    {
        return $this === self::BUSY;
    }

    public function isSynced(): bool
    {
        return $this === self::SYNCED;
    }
}
