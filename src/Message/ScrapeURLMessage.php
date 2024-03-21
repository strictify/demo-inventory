<?php

declare(strict_types=1);

namespace App\Message;

class ScrapeURLMessage implements AsyncMessageInterface
{
    public function __construct(public string $url)
    {
    }
}
