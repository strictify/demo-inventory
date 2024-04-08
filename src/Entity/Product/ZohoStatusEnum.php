<?php

declare(strict_types=1);

namespace App\Entity\Product;

enum ZohoStatusEnum: string
{
    case NOT_CONNECTED = 'not_connected';
    case BUSY = 'busy';
    case SYNCED = 'synced';
}
