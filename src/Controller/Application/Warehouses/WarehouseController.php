<?php

declare(strict_types=1);

namespace App\Controller\Application\Warehouses;

use App\Attribute\TurboFrame;
use App\Entity\Warehouse\Warehouse;
use App\Response\TurboRedirectResponse;
use App\Form\Entity\Warehouse\WarehouseType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\Warehouse\WarehouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WarehouseController extends AbstractController
{
    public function __construct(
        private WarehouseRepository $warehouseRepository,
    )
    {
    }

    #[Route('/', name: 'app_warehouses_list', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('app/warehouses/list.html.twig', [
            'warehouses' => $this->warehouseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'app_warehouses_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(WarehouseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->warehouseRepository->persistAndFlush($data);

            return $this->redirectToRoute('app_warehouses_list');
//            return new TurboRedirectResponse($this->generateUrl('app_warehouses_list'), $frame);
        }

        return $this->render('app/warehouses/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'app_warehouses_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Warehouse $warehouse): Response
    {
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->warehouseRepository->flush();

            return $this->redirectToRoute('app_warehouses_list');

//            return new TurboRedirectResponse($this->generateUrl('app_warehouses_list'), $frame);
        }

        return $this->render('app/warehouses/form.html.twig', [
            'form' => $form,
        ]);
    }
}
