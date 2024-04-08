<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use App\Turbo\Stream\Model\AbstractStream;

class BeforeStream extends AbstractStream
{
    protected function getAction(): string
    {
        return 'before';
    }
}
