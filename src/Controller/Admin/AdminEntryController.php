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
    public function admin(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('app/admin_non_supported.html.twig', [
            '_block' => $frame,
        ]);
    }
}
