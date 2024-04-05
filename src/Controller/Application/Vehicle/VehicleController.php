<?php

declare(strict_types=1);

namespace App\Controller\Application\Vehicle;

use App\Entity\Vehicle\Vehicle;
use App\Form\Entity\Vehicle\VehicleType;
use App\Form\Entity\Warehouse\WarehouseType;
use App\Repository\Vehicle\VehicleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VehicleController extends AbstractController
{
    public function __construct(
        private VehicleRepository $warehouseRepository,
    )
    {
    }

    #[Route('/', name: 'app_vehicles_list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('app/vehicles/list.html.twig', [
            'vehicles' => $this->warehouseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'app_vehicles_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(VehicleType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->warehouseRepository->persistAndFlush($data);

            return $this->redirectToRoute('app_vehicles_list');
//            return new TurboRedirectResponse($this->generateUrl('app_vehicles_list'), $frame);
        }

        return $this->render('app/vehicles/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'app_vehicles_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Vehicle $vehicle): Response
    {
        $form = $this->createForm(WarehouseType::class, $vehicle);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->warehouseRepository->flush();

            return $this->redirectToRoute('app_vehicles_list');

//            return new TurboRedirectResponse($this->generateUrl('app_vehicles_list'), $frame);
        }

        return $this->render('app/vehicles/form.html.twig', [
            'form' => $form,
        ]);
    }
}
