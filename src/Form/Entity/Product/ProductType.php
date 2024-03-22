<?php

declare(strict_types=1);

namespace App\Form\Entity\Product;

use Money\Money;
use App\Service\Security;
use App\Entity\Product\Product;
use App\Form\Type\QuillTextAreaType;
use App\DTO\Form\ProductInventoryDTO;
use Symfony\Component\Form\AbstractType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use App\Entity\Warehouse\WarehouseInventory;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\Warehouse\WarehouseInventoryRepository;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use function count;
use function array_map;
use function array_unique_objects;

/**
 * @extends AbstractType<Product>
 */
class ProductType extends AbstractType
{
    public function __construct(
        private Security $security,
        private WarehouseInventoryRepository $warehouseInventoryRepository,
    )
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => $this->factory(...),
            'label' => false,
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'get_value' => fn(Product $product) => $product->getName(),
            'update_value' => fn(string $name, Product $product) => $product->setName($name),
        ]);

        $builder->add('description', QuillTextAreaType::class, [
            'required' => false,
            'get_value' => fn(Product $product) => $product->getDescription(),
            'update_value' => fn(?string $description, Product $product) => $product->setDescription($description),
            'attr' => [
                'rows' => 5,
            ],
        ]);

        $builder->add('price', MoneyType::class, [
            'error_bubbling' => false,
            'constraints' => [
                new NotNull(),
                new Callback($this->validatePriceGreaterThanZero(...)),
            ],
            'get_value' => fn(Product $product) => $product->getPrice(),
            'update_value' => fn(Money $price, Product $product) => $product->setPrice($price),
        ]);

        $builder->add('inventory', CollectionType::class, [
            'entry_type' => ProductInventoryType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'label' => false,
            'add_button_value' => 'Add product',
            'get_value' => fn(Product $product) => $this->warehouseInventoryRepository->findBy(['product' => $product]),
            'add_value' => fn(ProductInventoryDTO $dto, Product $product) => $this->warehouseInventoryRepository->persist(new WarehouseInventory(
                warehouse: $dto->getWarehouse(),
                product: $product,
                quantity: $dto->getQuantity(),
            )),
            'remove_value' => fn(WarehouseInventory $inventory) => $this->warehouseInventoryRepository->remove($inventory),
            'constraints' => [
                new Valid(),
                new Callback($this->validateUniqueWarehouses(...))
            ]
        ]);
    }

    private function factory(string $name, ?string $description, Money $price): Product
    {
        $company = $this->security->getCompany();

        return new Product(
            company: $company,
            name: $name,
            description: $description,
            price: $price,
        );
    }

    /**
     * @param array<ProductInventoryDTO|WarehouseInventory> $data
     */
    private function validateUniqueWarehouses(array $data, ExecutionContextInterface $executionContext): void
    {
        $warehouses = array_map(fn(ProductInventoryDTO|WarehouseInventory $datum) => $datum->getWarehouse(), $data);
        $uniqueWarehouses = array_unique_objects($warehouses);
        if (count($warehouses) === count($uniqueWarehouses)) {
            return;
        }
        $executionContext->addViolation('Non-unique warehouses selected.');
    }

    private function validatePriceGreaterThanZero(Money $price, ExecutionContextInterface $executionContext): void
    {
        $amount = (int)$price->getAmount();
        if ($amount > 0) {
            return;
        }
        $executionContext->addViolation('Amount must be greater than zero.');
    }
}
