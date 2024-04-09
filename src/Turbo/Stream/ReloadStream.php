<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use Override;
use App\Turbo\Stream\Model\AbstractStream;

class ReloadStream extends AbstractStream
{
    #[Override]
    protected function getHtml(): ?string
    {
        $id = $this->getTargetId();

        return <<<HTML
        <meta data-controller="reload" data-reload-id-value="$id" >
HTML;
    }

    #[Override]
    protected function getAction(): string
    {
        return 'append';
    }
}
