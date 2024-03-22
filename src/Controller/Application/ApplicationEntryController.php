<?php

declare(strict_types=1);

namespace App\Controller\Application;

use App\Attribute\TurboFrame;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApplicationEntryController extends AbstractController
{
    #[Route('/', name: 'app_entry', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('app/dashboard.html.twig', [
            '_block' => $frame,
        ]);
    }

    #[Route('/left_sidebar', name: 'app_left_sidebar', methods: ['GET'])]
    public function leftSidebar(): Response
    {
        return $this->renderBlock('app/_app_partials.html.twig', 'left_sidebar');
    }

    #[Route('/header', name: 'app_header', methods: ['GET'])]
    public function header(): Response
    {
        return $this->renderBlock('app/_app_partials.html.twig', 'header');
    }
}
