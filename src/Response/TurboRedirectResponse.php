<?php
declare(strict_types=1);

namespace App\Response;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class TurboRedirectResponse extends RedirectResponse
{
    public function __construct(string $url, ?string $frame = null)
    {
        parent::__construct($url, headers: [
            'frame' => $frame,
        ]);
//        parent::__construct(status: 392, headers: ['location' => $url, 'frame' => $frame]);
    }
}
