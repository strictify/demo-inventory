<?php

declare(strict_types=1);

namespace App\Autowire\Doctrine;

interface OnClearEventListener
{
    /**
     * @api
     */
    public function onClear(): void;
}
