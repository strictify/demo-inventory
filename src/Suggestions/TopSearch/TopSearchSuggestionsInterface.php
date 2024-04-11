<?php

declare(strict_types=1);

namespace App\Suggestions\TopSearch;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: self::class)]
interface TopSearchSuggestionsInterface
{
    public function getGroupName(): string;

    /**
     * @return iterable<array{url: string, name: string}>
     */
    public function getResults(string $q, RouterInterface $router): iterable;
}
