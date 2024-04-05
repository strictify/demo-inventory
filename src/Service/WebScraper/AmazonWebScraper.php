<?php

declare(strict_types=1);

namespace App\Service\WebScraper;

use Override;
use Symfony\Component\Panther\DomCrawler\Crawler;
use function str_starts_with;

class AmazonWebScraper implements WebScraperInterface
{
    #[Override]
    public function supports(string $url): bool
    {
        return str_starts_with($url, 'https://amazon.com') || str_starts_with($url, 'https://www.amazon.com');
    }

    #[Override]
    public function getDetails(Crawler $crawler): array
    {
        $titleCrawler = $crawler->filter('span#productTitle')->first();
        $priceCrawler = $crawler->filter('#corePrice_feature_div')->first();

        $title = $titleCrawler->text();

        return [
            'name' => $title,
            'price' => $priceCrawler->text(),
        ];
    }
}
