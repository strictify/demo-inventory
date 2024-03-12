<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Attribute\TurboFrame;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminEntryController extends AbstractController
{
    #[Route('/', name: 'admin_entry', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            '_block' => $frame,
        ]);
    }

    #[Route('/left_sidebar', name: 'admin_left_sidebar', methods: ['GET'])]
    public function leftSidebar(): Response
    {
        return $this->renderBlock('admin/_admin_partials.html.twig', 'left_sidebar');
    }

    #[Route('/header', name: 'admin_header', methods: ['GET'])]
    public function header(): Response
    {
        return $this->renderBlock('admin/_admin_partials.html.twig', 'header');
    }
}
