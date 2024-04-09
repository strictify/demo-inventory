<?php

declare(strict_types=1);

namespace App\Entity\User;

use Override;
use Stringable;
use RuntimeException;
use App\Entity\IdTrait;
use BadMethodCallException;
use App\Entity\Company\Company;
use App\Entity\TenantAwareInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use function sprintf;
use function array_values;

class User implements UserInterface, PasswordAuthenticatedUserInterface, TenantAwareInterface, Stringable
{
    use IdTrait;

    /**
     * @param list<string> $roles
     */
    public function __construct(
        private string $email,
        private string $password,
        private string $firstName,
        private string $lastName,
        private array $roles = ['ROLE_USER'],
        private ?Company $company = null,
    )
    {
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf('%s %s', $this->firstName, $this->lastName);
    }

    #[Override]
    public function getCompany(): Company
    {
        return $this->company ?? throw new BadMethodCallException();
    }

    public function getId(): string
    {
        $uuid = $this->id ?? throw new RuntimeException();

        return (string)$uuid;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    #[Override]
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return non-empty-list<string>
     */
    #[Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_values(array_unique($roles));
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    #[Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @see UserInterface
     */
    #[Override]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }
}
