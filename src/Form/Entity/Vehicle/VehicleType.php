<?php

declare(strict_types=1);

namespace App\Form\Entity\Vehicle;

use App\Entity\Company\Company;
use App\Entity\Vehicle\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @extends AbstractType<Vehicle>
 */
class VehicleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => $this->factory(...),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('company', EntityType::class, [
            'class' => Company::class,
            'get_value' => fn(Vehicle $warehouse) => $warehouse->getCompany(),
            'update_value' => fn() => throw new TransformationFailedException(invalidMessage: 'You cannot change company.'),
        ]);

        $builder->add('name', TextType::class, [
            'get_value' => fn(Vehicle $warehouse) => $warehouse->getName(),
            'update_value' => fn(string $name, Vehicle $warehouse) => $warehouse->setName($name),
        ]);
    }

    private function factory(Company $company, string $name): Vehicle
    {
        return new Vehicle(
            company: $company,
            name: $name,
        );
    }
}
