<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use Override;
use App\Turbo\Stream\Model\AbstractStream;

class TurboVisitStream extends AbstractStream
{
    public function __construct(
        string $targetId,
        private string $url,
    )
    {
        parent::__construct($targetId);
    }

    #[Override]
    protected function getHtml(): ?string
    {
        $url = $this->url;

        return <<<HTML
        <meta data-controller="turbo-visit" data-turbo-visit-url-value="$url" >
HTML;
    }

    #[Override]
    protected function getAction(): string
    {
        return 'update';
    }
}
