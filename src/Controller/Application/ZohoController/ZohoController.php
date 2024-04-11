<?php

declare(strict_types=1);

namespace App\Controller\Application\ZohoController;

use App\Service\Security;
use App\Service\Zoho\ZohoClient;
use App\Turbo\Stream\ReplaceStream;
use App\Service\Mercure\StreamBuilder;
use App\Message\Zoho\ZohoDownloadAllMessage;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Company\CompanyRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ZohoController extends AbstractController
{
    public function __construct(
        private ZohoClient $zohoClient,
        private Security $security,
        private MessageBusInterface $messageBus,
        private readonly CompanyRepository $companyRepository,
        private StreamBuilder $streamBuilder,
    )
    {
    }

    #[Route('/connect', name: 'zoho_connect', methods: ['GET'])]
    public function connect(): Response
    {
        $company = $this->security->getCompany();
        $isConnected = (bool)$company->getZohoAccessToken();
        $connectUrl = $this->zohoClient->getConnectUrl();

        return $this->render('app/oauth2/zoho.html.twig', [
            'connect_url' => $connectUrl,
            'is_connected' => $isConnected,
            'company' => $company,
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

    #[Route('/download_all', name: 'zoho_download_all', methods: ['PUT'])]
    public function downloadAll(): Response
    {
        $company = $this->security->getCompany();
        $company->setZohoDownloading(true);
        $this->companyRepository->flush();

        $this->messageBus->dispatch(new ZohoDownloadAllMessage($company));

        return $this->connect();
    }

    #[Route('/refresh_zoho', name: 'zoho_refresh', methods: ['GET'])]
    public function refreshZoho(): Response
    {
        $company = $this->security->getCompany();
        $isConnected = (bool)$company->getZohoAccessToken();
        $connectUrl = $this->zohoClient->getConnectUrl();
        $html = $this->renderBlockView('app/oauth2/zoho.html.twig', 'main', [
            'connect_url' => $connectUrl,
            'is_connected' => $isConnected,
            'company' => $company,
        ]);

        return $this->streamBuilder->createResponse(
            new ReplaceStream('main', $html)
        );
    }
}
