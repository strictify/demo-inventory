<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Override;
use App\Attribute\IsFrame;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class IsFrameResolver implements ValueResolverInterface
{
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->getAttribute($argument)) {
            return [];
        }
        yield $request->headers->has('Turbo-Frame');
    }

    private function getAttribute(ArgumentMetadata $argument): ?IsFrame
    {
        $attributes = $argument->getAttributes(IsFrame::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof IsFrame ? $first : null;
    }
}
