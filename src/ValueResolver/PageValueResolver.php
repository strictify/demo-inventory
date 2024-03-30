<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Attribute\Page;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * Use this attribute to inject ``page`` query parameter to your controller.
 *
 * Defaults to 1, and ``page`` as name. Future versions will support (if needed) multiples of them with different names.
 *
 * <code>
 *   public function __invoke(#[Page] int $page)
 * </code>
 */
class PageValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->getAttribute($argument)) {
            return [];
        }
        $name = $argument->getName();

        yield $request->query->getInt($name, 1);
    }

    private function getAttribute(ArgumentMetadata $argument): ?Page
    {
        $attributes = $argument->getAttributes(Page::class, ArgumentMetadata::IS_INSTANCEOF);
        $first = $attributes[0] ?? null;

        return $first instanceof Page ? $first : null;
    }
}
