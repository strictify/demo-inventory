<?php

declare(strict_types=1);

namespace App\Service\Zoho;

use Override;
use LogicException;
use DateTimeImmutable;
use App\DTO\OAuth\Token;
use Psr\Log\LoggerInterface;
use InvalidArgumentException;
use App\Entity\Company\Company;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Mapper\Source\Source;
use App\Repository\Company\CompanyRepository;
use Symfony\Contracts\Service\ResetInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CuyZ\ValinorBundle\Configurator\Attributes\SupportDateFormats;
use CuyZ\ValinorBundle\Configurator\Attributes\AllowSuperfluousKeys;
use function ltrim;
use function is_int;
use function sprintf;
use function json_encode;

class ZohoClient implements ResetInterface
{
    private bool $tokenRefreshed = false;
    private string $baseUrl = 'https://accounts.zoho.eu';

    public function __construct(
        #[Autowire(env: 'ZOHO_CLIENT_ID')]
        private string $clientId,
        #[Autowire(env: 'ZOHO_CLIENT_SECRET')]
        private string $clientSecret,
        #[SupportDateFormats('Y-m-d', 'Y-m-d H:i'), AllowSuperfluousKeys]
        private TreeMapper $treeMapper,
        private HttpClientInterface $httpClient,
        private RouterInterface $router,
        private CompanyRepository $companyRepository,
        private LoggerInterface $logger,
    )
    {
    }

    #[Override]
    public function reset(): void
    {
        if (!$this->tokenRefreshed) {
            return;
        }
        $this->companyRepository->flush();
        $this->tokenRefreshed = false;
    }

    /**
     * @template T
     *
     * @param class-string<T> $dtoClass
     *
     * @return T
     */
    public function get(Company $company, string $url, string $dtoClass)
    {
        $data = $this->request($company, 'GET', $url);

        return $this->treeMapper->map($dtoClass, Source::json($data)->camelCaseKeys());
    }

    /**
     * @param non-empty-array<string, scalar> $data
     */
    public function put(Company $company, string $url, array $data): void
    {
        try {
            $this->request($company, 'PUT', $url, $data);
        } catch (InvalidArgumentException $e) {
            $this->logger->critical('Invalid data', context: [
                'exception' => $e,
                'url' => $url,
                'data' => $data,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function delete(Company $company, string $url): void
    {
        $this->request($company, 'DELETE', $url);
    }

    public function getConnectUrl(): string
    {
        return sprintf('%s/oauth/v2/auth?scope=%s&client_id=%s&state=testing&response_type=code&redirect_uri=%s&access_type=offline',
            $this->baseUrl,
            'ZohoInventory.FullAccess.all',
            $this->clientId,
            $this->generateRedirectUrl(),
        );
    }

    public function getToken(string $code, Company $company): void
    {
        $url = sprintf('%s/oauth/v2/token?code=%s&client_id=%s&client_secret=%s&redirect_uri=%s&grant_type=authorization_code',
            $this->baseUrl,
            $code,
            $this->clientId,
            $this->clientSecret,
            $this->generateRedirectUrl(),
        );

        $response = $this->httpClient->request('POST', $url);
        $data = $response->getContent();
        $dto = $this->treeMapper->map(Token::class, Source::json($data)->camelCaseKeys());

        $company->setZohoAccessToken($dto->getAccessToken());
        $company->setZohoRefreshToken($dto->getRefreshToken());
        if (is_int($expiresIn = $dto->getExpiresIn())) {
            $expiresAt = new DateTimeImmutable('+' . $expiresIn . ' seconds');
            $company->setZohoExpiresAt($expiresAt);
        }
        $this->tokenRefreshed = true;
    }

    /**
     * @param non-empty-array<string, scalar>|null $payload
     *
     * @throws InvalidArgumentException
     */
    private function request(Company $company, string $method, string $url, ?array $payload = null): string
    {
        $authToken = $company->getZohoAccessToken() ?? throw new LogicException('Zoho not configured.');
        $options = [
            'headers' => [
                'Authorization' => sprintf('Zoho-oauthtoken %s', $authToken),
            ],
        ];
        if ($payload !== null) {
            $options['json'] = $payload;
        }
        $url = sprintf('https://www.zohoapis.eu/inventory/v1/%s', ltrim($url, '/'));
        $response = $this->httpClient->request($method, $url, options: $options);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 400) {
            throw new InvalidArgumentException(message: json_encode($response->toArray(false), JSON_THROW_ON_ERROR));
        }
        if ($statusCode === 401) {
            $authToken = $this->refreshToken($company);
            $options['headers'] = [
                'Authorization' => sprintf('Zoho-oauthtoken %s', $authToken),
            ];
            $response = $this->httpClient->request($method, $url, $options);
            $statusCode = $response->getStatusCode();
            if ($statusCode === 400) {
                throw new InvalidArgumentException(message: json_encode($response->toArray(false), JSON_THROW_ON_ERROR));
            }
        }

        return $response->getContent(false);
    }

    private function refreshToken(Company $company): string
    {
        $url = sprintf('%s/oauth/v2/token?refresh_token=%s&client_id=%s&client_secret=%s&grant_type=refresh_token',
            $this->baseUrl,
            $company->getZohoRefreshToken() ?? throw new LogicException(),
            $this->clientId,
            $this->clientSecret,
        );

        $response = $this->httpClient->request('POST', $url);
        $content = $response->getContent();

        $dto = $this->treeMapper->map(Token::class, Source::json($content)->camelCaseKeys());
        $accessToken = $dto->getAccessToken() ?? throw new LogicException('Access token not found.');
        $company->setZohoAccessToken($accessToken);
        $this->tokenRefreshed = true;

        return $accessToken;
    }

    private function generateRedirectUrl(): string
    {
        return $this->router->generate('zoho_oauth2', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
