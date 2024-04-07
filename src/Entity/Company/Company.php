<?php

declare(strict_types=1);

namespace App\Entity\Company;

use Stringable;
use DateTimeImmutable;
use App\Entity\IdTrait;

/**
 * @psalm-type ZohoCredentials = array{access_token?: ?string, refresh_token?: ?string, expires_at?: ?DateTimeImmutable}
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
    )
    {
    }

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

    public function getZohoAccessToken(): ?string
    {
        return $this->zohoCredentials['access_token'] ?? null;
    }

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
}
