<?php

declare(strict_types=1);

namespace App\Controller\Application\ZohoController;

use App\Service\Security;
use App\Service\Zoho\ZohoClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ZohoController extends AbstractController
{
    public function __construct(
        private ZohoClient $zohoClient,
        private Security $security,
    )
    {
    }

    #[Route('/connect', name: 'zoho_connect', methods: ['GET'])]
    public function index(): Response
    {
        $connectUrl = $this->zohoClient->getConnectUrl();

        return $this->render('app/oauth2/zoho.html.twig', [
            'connect_url' => $connectUrl,
        ]);
    }

    #[Route('/oauth2', name: 'zoho_oauth2', methods: ['GET'])]
    public function auth(Request $request): Response
    {
        $code = $request->query->getString('code');
        $company = $this->security->getCompany();
        $this->zohoClient->getToken($code, $company);

        return $this->redirectToRoute('zoho_options');
    }

    #[Route('/options', name: 'zoho_options', methods: ['GET'])]
    public function zohoOptions(): Response
    {
        return $this->render('app/oauth2/zoho_options.html.twig');
    }
}
