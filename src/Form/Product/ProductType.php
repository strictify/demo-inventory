<?php

declare(strict_types=1);

namespace App\Form\Product;

use Money\Money;
use Webmozart\Assert\Assert;
use App\Entity\Product\Product;
use App\Entity\Company\Company;
use Symfony\Component\Form\AbstractType;
use Tbbc\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

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
        Assert::nullOrIsInstanceOf($data = $options['data'] ?? null, Product::class);

        $builder->add('company', EntityType::class, [
            'disabled' => (bool)$data,
            'class' => Company::class,
            'get_value' => fn(Product $product) => $product->getCompany(),
            'update_value' => fn(Company $company, Product $product) => throw new TransformationFailedException(invalidMessage: 'You cannot change company.'),
            'help' => (bool)$data ? 'Company cannot be changed' : null,
        ]);

        $builder->add('name', TextType::class, [
            'get_value' => fn(Product $product) => $product->getName(),
            'update_value' => fn(string $name, Product $product) => $product->setName($name),
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

    private function factory(Company $company, string $name, Money $price): Product
    {
        return new Product(
            company: $company,
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
