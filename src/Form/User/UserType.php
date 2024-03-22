<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends AbstractType<User>
 */
class UserType extends AbstractType
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
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
        $builder->add('firstName', TextType::class, [
            'get_value' => fn(User $user) => $user->getFirstName(),
            'update_value' => fn(string $firstName, User $user) => $user->setFirstName($firstName),
        ]);

        $builder->add('lastName', TextType::class, [
            'get_value' => fn(User $user) => $user->getLastName(),
            'update_value' => fn(string $lastName, User $user) => $user->setLastName($lastName),
        ]);

        $builder->add('email', EmailType::class, [
            'get_value' => fn(User $user) => $user->getEmail(),
            'update_value' => fn(string $email, User $user) => $user->setEmail($email),
            'constraints' => [
                new Email(),
            ],
        ]);

        $builder->add('password', PasswordType::class, [
            'required' => false,
            'get_value' => fn() => null,
            'update_value' => function (?string $newPassword, User $user) {
                if (null === $newPassword) {
                    return;
                }
                $hashedPassword = $this->passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            },
            'constraints' => [
                new Length(min: 5),
            ],
        ]);
    }

    private function factory(string $firstName, string $lastName, string $email, ?string $password): User
    {
        if (null === $password) {
            throw new TransformationFailedException(invalidMessage: 'You must set password for new user.');
        }
        $user = new User(email: $email, password: '', firstName: $firstName, lastName: $lastName);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        return $user;
    }
}
