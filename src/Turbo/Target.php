<?php

declare(strict_types=1);

namespace App\Turbo;

use App\Turbo\Stream\UpdateStream;
use App\Turbo\Stream\ReplaceStream;
use App\Turbo\Stream\SetAttributeStream;
use App\Turbo\Stream\Model\StreamInterface;

/**
 * @see https://turbo.hotwired.dev/handbook/streams for documentation
 */
class Target
{
    public function __construct(
        private string $targetId,
    )
    {
    }

    public function update(string $html): StreamInterface
    {
        return new UpdateStream($this->targetId, $html);
    }

    public function empty(): StreamInterface
    {
        return new UpdateStream($this->targetId, null);
    }

    public function replace(string $html): StreamInterface
    {
        return new ReplaceStream($this->targetId, $html);
    }

    public function setAttribute(string $name, null|string|int $value = null): StreamInterface
    {
        return new SetAttributeStream($this->targetId, $name, $value);
    }
}
