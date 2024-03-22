<?php

declare(strict_types=1);

namespace App\Form\Warehouse;

use App\Service\Security;
use App\Entity\Warehouse\Warehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * @extends AbstractType<Warehouse>
 */
class WarehouseType extends AbstractType
{
    public function __construct(
        private Security $security,
    )
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => $this->factory(...),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'get_value' => fn(Warehouse $warehouse) => $warehouse->getName(),
            'update_value' => fn(string $name, Warehouse $warehouse) => $warehouse->setName($name),
        ]);
    }

    private function factory(string $name): Warehouse
    {
        return new Warehouse(
            company: $this->security->getCompany(),
            name: $name,
        );
    }
}
