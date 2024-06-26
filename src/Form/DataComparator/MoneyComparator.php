<?php

declare(strict_types=1);

namespace App\Form\DataComparator;

use Override;
use Money\Money;
use Strictify\FormMapper\Service\Comparator\DataComparatorInterface;

class MoneyComparator implements DataComparatorInterface
{
    #[Override]
    public function isEqual($first, $second): bool
    {
        if ($first instanceof Money && $second instanceof Money) {
            return $first->getAmount() === $second->getAmount() && $first->getCurrency()->getCode() === $second->getCurrency()->getCode();
        }

        return false;
    }
}
