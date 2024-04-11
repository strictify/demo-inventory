<?php

namespace App\Form\Autocomplete;

use Override;
use App\Entity\Category\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;
use Symfony\UX\Autocomplete\Form\BaseEntityAutocompleteType;

/**
 * @extends AbstractType<Category>
 */
#[AsEntityAutocompleteField(alias: 'categories')]
class CategoriesAutocompleteType extends AbstractType
{
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'class' => Category::class,
            'multiple' => true,
            'choice_label' => fn(Category $category) => $category->getName(),
            'searchable_fields' => ['name'],
        ]);
    }

    #[Override]
    public function getParent(): string
    {
        return BaseEntityAutocompleteType::class;
    }
}
