<?php

namespace App\DTO\OAuth;

class Token
{
    /**
     * @param non-empty-string|null $accessToken
     * @param non-empty-string|null $refreshToken
     * @param int|null $expiresIn
     */
    public function __construct(
        private ?string $accessToken = null,
        private ?string $refreshToken = null,
        private ?int $expiresIn = null,
    )
    {
    }

    /**
     * @return non-empty-string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @return non-empty-string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }
}
