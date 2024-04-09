<?php

declare(strict_types=1);

namespace App\Entity\Company;

use Override;
use Stringable;
use DateTimeImmutable;
use App\Entity\IdTrait;

/**
 * @psalm-type ZohoCredentials = array{access_token?: non-empty-string|null, refresh_token?: ?string, expires_at?: ?DateTimeImmutable}
 */
class Company implements Stringable
{
    use IdTrait;

    /**
     * @param ZohoCredentials $zohoCredentials
     */
    public function __construct(
        private string $name,
        private array $zohoCredentials = [],
        private bool $zohoDownloading = false,
    )
    {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return non-empty-string|null
     */
    public function getZohoAccessToken(): ?string
    {
        return $this->zohoCredentials['access_token'] ?? null;
    }

    /**
     * @param non-empty-string|null $accessToken
     */
    public function setZohoAccessToken(?string $accessToken): void
    {
        $this->zohoCredentials['access_token'] = $accessToken;
    }

    public function getZohoRefreshToken(): ?string
    {
        return $this->zohoCredentials['refresh_token'] ?? null;
    }

    public function setZohoRefreshToken(?string $refreshToken): void
    {
        $this->zohoCredentials['refresh_token'] = $refreshToken;
    }

    public function getZohoExpiresAt(): ?DateTimeImmutable
    {
        return $this->zohoCredentials['expires_at'] ?? null;
    }

    public function setZohoExpiresAt(?DateTimeImmutable $expiresAt): void
    {
        $this->zohoCredentials['expires_at'] = $expiresAt;
    }

    public function isZohoDownloading(): bool
    {
        return $this->zohoDownloading;
    }

    public function setZohoDownloading(bool $zohoDownloading): void
    {
        $this->zohoDownloading = $zohoDownloading;
    }
}
