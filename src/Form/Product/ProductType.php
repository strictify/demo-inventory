<?php

declare(strict_types=1);

namespace App\Form\Product;

use Money\Money;
use App\Entity\Product\Product;
use Symfony\Component\Form\AbstractType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @extends AbstractType<Product>
 */
class ProductType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'factory' => $this->factory(...),
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'get_value' => fn(Product $user) => $user->getName(),
            'update_value' => fn(string $name, Product $user) => $user->setName($name),
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
    }

    private function factory(string $name, Money $price): Product
    {
        return new Product(
            name: $name,
            price: $price,
        );
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
