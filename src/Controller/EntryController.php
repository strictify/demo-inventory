<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntryController extends AbstractController
{
    #[Route('/', name: 'app_entry')]
    public function index(): Response
    {
        return $this->redirectToRoute('admin_entry');
    }
}
