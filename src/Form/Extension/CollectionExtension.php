<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Override;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use function count;

/**
 * @extends AbstractTypeExtension<CollectionType>
 */
class CollectionExtension extends AbstractTypeExtension
{
    #[Override]
    public static function getExtendedTypes(): iterable
    {
        yield CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'min' => 0,
            'add_button_value' => 'Add',
            'button_position' => 'after',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $nrOfChildren = count($form);
        $view->vars['next_index'] = $nrOfChildren;
        $view->vars['minimum'] = $options['min'] ?? 0;
        $view->vars['add_button_value'] = $options['add_button_value'];
        $view->vars['button_position'] = $options['button_position'];
    }
}
