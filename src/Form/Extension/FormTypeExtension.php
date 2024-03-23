<?php

declare(strict_types=1);

namespace App\Form\Extension;

use Override;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use function count;

/**
 * @extends AbstractTypeExtension<CollectionType>
 */
class FormTypeExtension extends AbstractTypeExtension
{
    #[Override]
    public static function getExtendedTypes(): iterable
    {
        yield FormType::class;
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'collection_entry_class' =>  null,
        ]);
    }

    #[Override]
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['collection_entry_class'] = $options['collection_entry_class'];
    }
}
