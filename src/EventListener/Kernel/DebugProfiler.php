<?php

declare(strict_types=1);

namespace App\EventListener\Kernel;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: KernelEvents::RESPONSE)]
class DebugProfiler
{
    public function __construct(
        private KernelInterface $kernel,
    )
    {
    }

    public function __invoke(ResponseEvent $event): void
    {
        return;
        if (!$this->kernel->isDebug()) {
            return;
        }
        $request = $event->getRequest();

        if (!$request->isXmlHttpRequest() && !$request->headers->has('Turbo-Frame')) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Symfony-Debug-Toolbar-Replace', '1');
    }
}
