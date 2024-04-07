<?php

declare(strict_types=1);

namespace App\Form\Entity\Product;

use Override;
use App\Entity\Warehouse\Warehouse;
use App\DTO\Form\ProductInventoryDTO;
use Symfony\Component\Form\AbstractType;
use App\Entity\Warehouse\WarehouseInventory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

/**
 * @extends AbstractType<void>
 */
class ProductInventoryType extends AbstractType
{
    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => fn(Warehouse $warehouse, int $quantity) => new ProductInventoryDTO($warehouse, $quantity),
        ]);
    }

    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('warehouse', EntityType::class, [
            'placeholder' => '--',
            'class' => Warehouse::class,
            'get_value' => fn(ProductInventoryDTO|WarehouseInventory $data) => $data->getWarehouse(),
            'update_value' => fn(Warehouse $warehouse, ProductInventoryDTO|WarehouseInventory $data) => $data->setWarehouse($warehouse),
        ]);

        $builder->add('quantity', IntegerType::class, [
            'get_value' => fn(ProductInventoryDTO|WarehouseInventory $data) => $data->getQuantity(),
            'update_value' => fn(int $quantity, ProductInventoryDTO|WarehouseInventory $data) => $data->setQuantity($quantity),
            'collection_entry_class' => 'col-3',
            'attr' => [
                'min' => 1,
            ],
        ]);
    }
}
