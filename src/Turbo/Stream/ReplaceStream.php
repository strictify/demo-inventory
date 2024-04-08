<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use App\Turbo\Stream\Model\AbstractStream;

class ReplaceStream extends AbstractStream
{
    protected function getAction(): string
    {
        return 'replace';
    }
}
