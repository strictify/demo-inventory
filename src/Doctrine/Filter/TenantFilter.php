<?php

declare(strict_types=1);

namespace App\Doctrine\Filter;

use Override;
use App\Entity\TenantAwareInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use function sprintf;

class TenantFilter extends SQLFilter
{
    #[Override]
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if (!$reflectionClass = $targetEntity->reflClass) {
            return '';
        }
        if (!$reflectionClass->implementsInterface(TenantAwareInterface::class)) {
            return '';
        }

        $tenantId = $this->getParameter('tenant_id');

        return sprintf('%s.company_id = %s', $targetTableAlias, $tenantId);
    }
}
