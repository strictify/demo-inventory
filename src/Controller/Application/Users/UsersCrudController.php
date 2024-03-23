<?php

declare(strict_types=1);

namespace App\Controller\Application\Users;

use App\Entity\User\User;
use App\Attribute\TurboFrame;
use App\Form\Entity\User\UserType;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersCrudController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    #[Route('/', name: 'app_users_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('app/users/list.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'app_users_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->userRepository->persistAndFlush($data);

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render('app/users/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'app_users_update', methods: ['GET', 'POST'])]
    public function update(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            return $this->redirectToRoute('app_users_list');
        }

        return $this->render('app/users/form.html.twig', [
            'form' => $form,
        ]);
    }
}
