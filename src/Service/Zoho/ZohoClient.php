<?php

declare(strict_types=1);

namespace App\Service\Zoho;

use LogicException;
use DateTimeImmutable;
use App\DTO\OAuth\Token;
use App\Entity\Company\Company;
use CuyZ\Valinor\Mapper\TreeMapper;
use CuyZ\Valinor\Mapper\Source\Source;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use CuyZ\ValinorBundle\Configurator\Attributes\SupportDateFormats;
use CuyZ\ValinorBundle\Configurator\Attributes\AllowSuperfluousKeys;
use function ltrim;
use function is_int;
use function sprintf;

class ZohoClient
{
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
    )
    {
    }

    /**
     * @template T
     * @param class-string<T> $dtoClass
     *
     * @return T
     */
    public function get(Company $company, string $url, string $dtoClass)
    {
        $url = ltrim($url, '/');
        $url = sprintf('https://www.zohoapis.eu/inventory/v1/%s', $url);
        $authToken = $company->getZohoAccessToken() ?? throw new LogicException('Zoho not configured.');

        $data = $this->doGet($company, $url, $authToken);

        return $this->treeMapper->map($dtoClass, Source::json($data)->camelCaseKeys());
    }

    private function doGet(Company $company, string $url, string $accessToken): string
    {
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => sprintf('Zoho-oauthtoken %s', $accessToken),
            ],
        ]);
        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            return $response->getContent();
        }
        if ($statusCode === 401) {
            $accessToken = $this->refreshToken($company);
            $response = $this->httpClient->request('GET', $url, [
                'headers' => [
                    'Authorization' => sprintf('Zoho-oauthtoken %s', $accessToken),
                ],
            ]);

            return $response->getContent();
        }

        throw new LogicException(sprintf('Status code %s not supported.', $statusCode));
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
        $this->companyRepository->flush();

        return $accessToken;
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

        $this->companyRepository->flush();
    }

    private function generateRedirectUrl(): string
    {
        return $this->router->generate('zoho_oauth2', referenceType: UrlGeneratorInterface::ABSOLUTE_URL);
    }
}
