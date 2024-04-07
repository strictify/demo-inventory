<?php
declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

/**
 * @psalm-suppress PropertyNotSetInConstructor - bug in Symfony itself
 */
class TurboRedirectResponse extends Response
{

    public function __construct(string $url, ?string $frame = null)
    {
        parent::__construct($url, status: 204, headers: [
            'frame' => $frame,
            'redirect-url' => $url,
        ]);
//        parent::__construct(status: 392, headers: ['location' => $url, 'frame' => $frame]);
    }
}
