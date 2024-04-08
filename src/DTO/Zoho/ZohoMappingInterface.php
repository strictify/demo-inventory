<?php

declare(strict_types=1);

namespace App\DTO\Zoho;

/**
 * @template T of ZohoSingleResultInterface
 */
interface ZohoMappingInterface
{
    /**
     * @return T
     */
    public function getOne(): object;

    /**
     * @return list<T>
     */
    public function getMany(): array;
}
