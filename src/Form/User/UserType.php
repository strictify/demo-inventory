<?php

declare(strict_types=1);

namespace App\Form\User;

use App\Entity\User\User;
use App\Repository\User\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @extends AbstractType<User>
 */
class UserType extends AbstractType
{
    public function __construct(
        private UserRepository $userRepository,
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
                if (!$newPassword) {
                    return;
                }
                $this->userRepository->upgradePassword($user, $newPassword);
            },
            'constraints' => [
                new Length(min: 5),
            ],
        ]);
    }

    private function factory(string $email, ?string $password): User
    {
        if (!$password) {
            throw new TransformationFailedException(invalidMessage: 'You must set password for new user.');
        }

        return $this->userRepository->create($email, $password);
    }
}
