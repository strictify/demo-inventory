<?php
declare(strict_types=1);

namespace App\EventListener\Kernel;

use App\Response\TurboRedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use function str_contains;

#[AsEventListener(event: KernelEvents::RESPONSE)]
class RedirectListener
{
    public function __invoke(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        if (!$response->isRedirect()) {
            return;
        }
        $request = $event->getRequest();
        if (!$request->headers->has('Turbo-Frame')) {
            return;
        }
        $location = $response->headers->get('Location');
        if (null === $location) {
            return;
        }
        if (str_contains($location, '/login')) {
            $event->setResponse(new TurboRedirectResponse($location));
        }
    }
}
