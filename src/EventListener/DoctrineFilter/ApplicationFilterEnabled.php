<?php

declare(strict_types=1);

namespace App\EventListener\DoctrineFilter;

use App\Entity\User\User;
use App\Service\Security;
use Webmozart\Assert\Assert;
use App\Doctrine\Filter\TenantFilter;
use Doctrine\ORM\EntityManagerInterface;
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

    #[AsEventListener(event: KernelEvents::REQUEST, priority: -600)]
    public function appFilter(RequestEvent $event): void
    {
        $uri = $event->getRequest()->getRequestUri();
        if (!str_starts_with($uri, '/app')) {
            return;
        }

        $user = $this->security->getUser();
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
