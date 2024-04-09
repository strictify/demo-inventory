<?php

declare(strict_types=1);

namespace App\Service\Mercure;

use Exception;
use Psr\Log\LoggerInterface;
use App\Entity\Company\Company;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use App\Turbo\Stream\Model\StreamInterface;
use Symfony\Component\HttpFoundation\Response;
use function implode;
use function sprintf;
use function array_map;

class StreamBuilder
{
    public function __construct(
        private HubInterface $hub,
        private LoggerInterface $logger,
    )
    {
    }

    public function createResponse(StreamInterface ...$streams): Response
    {
        $merged = $this->merge(...$streams);
        $response = new Response($merged);
        $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');

        return $response;
    }

    public function pushToApp(Company $company, StreamInterface ...$streams): void
    {
        $topic = sprintf('app-%s', $company->getId());

        $this->push($topic, ...$streams);
    }

    public function push(string $topic, StreamInterface ...$streams): void
    {
        $merged = $this->merge(...$streams);
        try {
            $update = new Update(
                $topic,
                $merged,
            );
            $this->hub->publish($update);
        } catch (Exception $e) {
            // we don't want code interruption if hub failed
            $this->logger->alert(sprintf('Mercure hub failed with message: %s', $e->getMessage()), context: [
                'message' => $merged,
                'exception' => $e,
            ]);
        }
    }

    private function merge(StreamInterface ...$streams): string
    {
        $asArray = array_map(static fn(StreamInterface $stream) => $stream->generate(), $streams);

        return implode("\n", $asArray);
    }
}
