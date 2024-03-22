<?php

declare(strict_types=1);

namespace App\EventListener\DoctrineFilter;

use App\Entity\User\User;
use Webmozart\Assert\Assert;
use App\Doctrine\Filter\TenantFilter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use function str_starts_with;

class ApplicationFilterEnabled
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    )
    {
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function appFilter(RequestEvent $event): void
    {
        $uri = $event->getRequest()->getRequestUri();
        if (!str_starts_with($uri, '/app')) {
            return;
        }

        Assert::isInstanceOf($user = $this->security->getUser(), User::class);
        $companyId = $user->getCompany()->getId();
        $this->enableFilter($companyId);
    }

    /**
     * @see TenantFilter
     */
    private function enableFilter(string $id): void
    {
        $filter = $this->em->getFilters()->enable('tenant');
        $filter->setParameter('tenant_id', $id);
    }
}
