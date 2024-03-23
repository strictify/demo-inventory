<?php

declare(strict_types=1);

namespace App\Definition;

use App\Kernel;
use Twig\Environment;
use Symfony\Component\HttpFoundation\RequestStack;
use function is_string;
use function array_merge;

class TwigDecorator extends Environment
{
    private ?RequestStack $requestStack = null;

    public function render($name, array $context = []): string
    {
        $frame = $this->requestStack?->getCurrentRequest()?->headers->get('Turbo-Frame');
        $extraParams = [
            '_frame' => is_string($frame) && $frame !== '' ? $frame : null,
        ];

        return parent::render($name, array_merge($extraParams, $context));
    }

    /**
     * @see Kernel::process()
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }
}
