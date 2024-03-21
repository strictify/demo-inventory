<?php

declare(strict_types=1);

namespace App\Controller\Admin\Warehouses;

use App\Attribute\TurboFrame;
use App\Entity\Warehouse\Warehouse;
use App\Form\Product\WarehouseType;
use App\Response\TurboRedirectResponse;
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

    #[Route('/', name: 'admin_warehouses_list', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('admin/warehouses/list.html.twig', [
            '_block' => $frame,
            'warehouses' => $this->warehouseRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_warehouses_create', methods: ['GET', 'POST'])]
    public function create(#[TurboFrame] ?string $frame, Request $request): Response
    {
        $form = $this->createForm(WarehouseType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->warehouseRepository->persistAndFlush($data);

            return new TurboRedirectResponse($this->generateUrl('admin_warehouses_list'), $frame);
        }

        return $this->render('admin/warehouses/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'admin_warehouses_update', methods: ['GET', 'POST'])]
    public function update(#[TurboFrame] ?string $frame, Request $request, Warehouse $warehouse): Response
    {
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->warehouseRepository->flush();

            return new TurboRedirectResponse($this->generateUrl('admin_warehouses_list'), $frame);
        }

        return $this->render('admin/warehouses/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }
}
