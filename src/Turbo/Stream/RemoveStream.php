<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use Override;
use App\Turbo\Stream\Model\AbstractStream;

class RemoveStream extends AbstractStream
{
    #[Override]
    protected function getAction(): string
    {
        return 'remove';
    }
}
