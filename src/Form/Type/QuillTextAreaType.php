<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * @extends AbstractType<void>
 */
class QuillTextAreaType extends AbstractType
{
    public function getParent(): string
    {
        return TextareaType::class;
    }
}
