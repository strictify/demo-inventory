<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function in_array;

class EntryController extends AbstractController
{
    #[Route('/', name: 'entry', methods: ['GET'])]
    public function index(#[CurrentUser] User $user): Response
    {
        if (in_array('ROLE_COMPANY', $user->getRoles(), true)) {
            return $this->redirectToRoute('app_entry');
        }
        return $this->redirectToRoute('admin_entry');
    }
}
