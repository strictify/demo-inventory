<?php
declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TurboRedirectResponse extends Response
{
    public function __construct(string $url, ?string $frame = null)
    {
        parent::__construct(status: 204, headers: ['location' => $url, 'frame' => $frame]);
    }
}
