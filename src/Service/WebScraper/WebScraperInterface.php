<?php

declare(strict_types=1);

namespace App\Service\WebScraper;

use Symfony\Component\Panther\DomCrawler\Crawler;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag(name: self::class)]
interface WebScraperInterface
{
    public function supports(string $url): bool;

    /**
     * @return array{name: string, price: string}
     */
    public function getDetails(Crawler $crawler): array;
}
