<?php

namespace App\DTO\OAuth;

class Token
{
    public function __construct(
        private ?string $accessToken = null,
        private ?string $refreshToken = null,
        private ?int $expiresIn = null,
    )
    {
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }
}
