<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use Throwable;
use InvalidArgumentException;
use App\Message\ScrapeURLMessage;
use Psr\Cache\CacheItemInterface;
use App\Exception\ScrapeException;
use Symfony\Component\Panther\Client;
use Symfony\Contracts\Cache\CacheInterface;
use App\Service\WebScraper\WebScraperInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use function sprintf;
use function base64_encode;

class WebScraper
{
    /**
     * @param iterable<WebScraperInterface> $scrapers
     */
    public function __construct(
        #[TaggedIterator(tag: WebScraperInterface::class)]
        private iterable $scrapers,
        private CacheInterface $cache,
    )
    {
    }

    #[AsMessageHandler]
    public function __invoke(ScrapeURLMessage $message): void
    {
        try {
            $url = $message->url;
            $data = $this->scrape($url);

//            dump($data['name'], $data['price']);
        } catch (Throwable $e) {
//            dump($e->getMessage());
            throw new UnrecoverableMessageHandlingException(previous: $e);
        }
    }

    /**
     * @return array{name: string, price: string, screenshot: string}
     *
     * @throws ScrapeException
     */
    public function scrape(string $url): array
    {
        try {
            $key = base64_encode($url);

            return $this->cache->get($key, function (CacheItemInterface $item) use ($url) {
                $item->expiresAt(new DateTime('+1 minute'));
                $data = $this->doScrape($url);
                $item->set($data);

                return $data;
            });
        } catch (Throwable $e) {
            throw new ScrapeException(message: $e->getMessage(), previous: $e);
        }
    }

    /**
     * @return array{name: string, price: string, screenshot: string}
     */
    private function doScrape(string $url): array
    {
        $client = Client::createChromeClient();
        $scraper = $this->findScraperForUrl($url);
        $crawler = $client->request('GET', $url);
        try {
            $results = $scraper->getDetails($crawler);
            // we don't want to reveal internal messages
        } catch (Throwable $e) {
            throw new ScrapeException('Scraper failure, required elements could not be found.', previous: $e);
        }

        return [
            'name' => $results['name'],
            'price' => $results['price'],
            'screenshot' => $client->takeScreenshot(),
        ];
    }

    private function findScraperForUrl(string $url): WebScraperInterface
    {
        foreach ($this->scrapers as $scraper) {
            if ($scraper->supports($url)) {
                return $scraper;
            }
        }

        throw new InvalidArgumentException(sprintf('No registered scraper for url: %s', $url));
    }
}
