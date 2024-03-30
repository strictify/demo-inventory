<?php

declare(strict_types=1);

namespace App\Decorator;

use Override;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'router')]
class FilterPassThruRouterDecorator implements RouterInterface, WarmableInterface
{
    /**
     * @param array<string> $passQueryData
     */
    public function __construct(
        private Router $router,
        private RequestStack $requestStack,
    )
    {
    }

    public function setContext(RequestContext $context): void
    {
        $this->router->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->router->getContext();
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->router->getRouteCollection();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $keyword = '_filters';
        $request = $this->requestStack->getCurrentRequest();
        if (isset($parameters[$keyword]) && $request) {
            $parameters = $this->doProcess($parameters, $request);
            unset($parameters[$keyword]);
        }

        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function match(string $pathinfo): array
    {
        return $this->router->match($pathinfo);
    }

    private function doProcess(array $parameters, Request $request): array
    {
        foreach (['filters'] as $passQueryDatum) {
            /** @var array<string>|null $values */
            $values = $request->query->all($passQueryDatum);
            if ($values) {
                $parameters[$passQueryDatum] = $values;
            }
        }

        return $parameters;
    }

    #[Override]
    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        return $this->router->warmUp($cacheDir, $buildDir);
    }
}
