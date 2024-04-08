<?php

declare(strict_types=1);

namespace App\Turbo\Stream;

use App\Turbo\Stream\Model\AbstractStream;

class CloseModalStream extends AbstractStream
{
    public function __construct()
    {
        parent::__construct('modal');
    }

    protected function getHtml(): ?string
    {
        return <<<HTML
        <meta data-controller="close-modal">
HTML;
    }

    protected function getAction(): string
    {
        return 'append';
    }
}
