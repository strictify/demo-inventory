<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Override;
use App\Attribute\MainRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsDecorator(decorates: 'argument_resolver.request')]
class MainRequestResolver implements ValueResolverInterface
{
    public function __construct(
        private ValueResolverInterface $resolver,
        private RequestStack $requestStack,
    )
    {
    }

    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->getAttribute($argument)) {
            return $this->resolver->resolve($request, $argument);
        }

        return [$this->requestStack->getMainRequest()];
    }

    private function getAttribute(ArgumentMetadata $argument): ?MainRequest
    {
        $attributes = $argument->getAttributes(MainRequest::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof MainRequest ? $first : null;
    }
}
