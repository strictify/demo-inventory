<?php

declare(strict_types=1);

namespace App\Controller\Application;

use App\Service\Security;
use App\Attribute\TurboFrame;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use function sprintf;

class ApplicationEntryController extends AbstractController
{
    #[Route('/dashboard', name: 'app_entry', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('app/dashboard.html.twig', [
            '_block' => $frame,
        ]);
    }

    #[Route('/left_sidebar', name: 'app_left_sidebar', methods: ['GET'])]
    public function leftSidebar(RequestStack $requestStack): Response
    {
        $mainRequest = $requestStack->getMainRequest();
        $routeName = $mainRequest?->attributes->getString('_route');

        return $this->renderBlock('app/_app_partials.html.twig', 'left_sidebar', [
            'route' => $routeName,
        ]);
    }

    #[Route('/header', name: 'app_header', methods: ['GET'])]
    public function header(): Response
    {
        return $this->renderBlock('app/_app_partials.html.twig', 'header');
    }

    #[Route('/mercure_listener', name: 'app_mercure_listener', methods: ['GET'])]
    public function mercureListener(Security $security): Response
    {
        $company = $security->getCompany();
        $topic = sprintf('app-%s', $company->getId());

        return $this->renderBlock('app/_app_partials.html.twig', 'mercure_listener', [
            'topic' => $topic,
        ]);
    }
}
