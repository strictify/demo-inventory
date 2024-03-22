<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User\User;
use Webmozart\Assert\Assert;
use App\Entity\Company\Company;
use Symfony\Bundle\SecurityBundle\Security as BaseSecurity;

class Security
{
    public function __construct(
        private BaseSecurity $baseSecurity,
    )
    {
    }

    public function getUser(): User
    {
        Assert::isInstanceOf($user = $this->baseSecurity->getUser(), User::class);

        /** @noinspection PhpIncompatibleReturnTypeInspection - PHPStorm doesn't understand psalm-assert */
        return $user;
    }

    public function getCompany(): Company
    {
        return $this->getUser()->getCompany();
    }
}
