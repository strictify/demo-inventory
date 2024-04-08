<?php

declare(strict_types=1);

namespace App\Turbo\Stream\Model;

interface StreamInterface
{
    public function generate(): string;
}
