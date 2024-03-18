<?php

declare(strict_types=1);

namespace App\Controller\Admin\Products;

use App\Attribute\TurboFrame;
use App\Entity\Product\Product;
use App\Form\Product\ProductType;
use App\Response\TurboRedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'admin_products_list', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('admin/products/list.html.twig', [
            '_block' => $frame,
            'products' => $this->productRepository->findAll(),
        ]);
    }

    #[Route('/create', name: 'admin_products_create', methods: ['GET', 'POST'])]
    public function create(#[TurboFrame] ?string $frame, Request $request): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->productRepository->persistAndFlush($data);

            return new TurboRedirectResponse($this->generateUrl('admin_products_list'), $frame);
            return $this->redirectToRoute('admin_products_list');
        }

        return $this->render('admin/products/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'admin_products_update', methods: ['GET', 'POST'])]
    public function update(#[TurboFrame] ?string $frame, Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->flush();

//            return new TurboRedirectResponse($this->generateUrl('admin_products_list'), $frame);

            return $this->redirectToRoute('admin_products_list', status: 303);
//            return new TurboRedirectResponse($this->generateUrl('admin_products_list'), $frame);
        }

        return $this->render('admin/products/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }
}
