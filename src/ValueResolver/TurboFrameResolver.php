<?php

declare(strict_types=1);

namespace App\ValueResolver;

use Override;
use App\Attribute\TurboFrame;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class TurboFrameResolver implements ValueResolverInterface
{
    #[Override]
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->getAttribute($argument)) {
            return [];
        }
        $frameId = $request->headers->get('Turbo-Frame');
        if (null === $frameId && !$argument->isNullable()) {
            throw new NotFoundHttpException('Frame not found.');
        }

        yield $frameId;
    }

    private function getAttribute(ArgumentMetadata $argument): ?TurboFrame
    {
        $attributes = $argument->getAttributes(TurboFrame::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof TurboFrame ? $first : null;
    }
}
