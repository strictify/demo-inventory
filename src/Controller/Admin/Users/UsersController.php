<?php

declare(strict_types=1);

namespace App\Controller\Admin\Users;

use App\Entity\User\User;
use App\Form\User\UserType;
use App\Attribute\TurboFrame;
use App\Repository\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UsersController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    #[Route('/', name: 'admin_users_list', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('admin/users/list.html.twig', [
            '_block' => $frame,
            'users' => $this->userRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_users_create', methods: ['GET', 'POST'])]
    public function create(#[TurboFrame] ?string $frame, Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->userRepository->persistAndFlush($data);

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/users/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'admin_users_update', methods: ['GET', 'POST'])]
    public function update(#[TurboFrame] ?string $frame, Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->flush();

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/users/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }
}
