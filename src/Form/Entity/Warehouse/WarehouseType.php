<?php

declare(strict_types=1);

namespace App\Form\Entity\Warehouse;

use Override;
use App\Entity\Company\Company;
use App\Entity\Warehouse\Warehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @extends AbstractType<Warehouse>
 */
class WarehouseType extends AbstractType
{
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => $this->factory(...),
        ]);
    }

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('company', EntityType::class, [
            'class' => Company::class,
            'get_value' => fn(Warehouse $warehouse) => $warehouse->getCompany(),
            'update_value' => fn() => throw new TransformationFailedException(invalidMessage: 'You cannot change company.'),
        ]);

        $builder->add('name', TextType::class, [
            'get_value' => fn(Warehouse $warehouse) => $warehouse->getName(),
            'update_value' => fn(string $name, Warehouse $warehouse) => $warehouse->setName($name),
        ]);
    }

    private function factory(Company $company, string $name): Warehouse
    {
        return new Warehouse(
            company: $company,
            name: $name,
        );
    }
}
