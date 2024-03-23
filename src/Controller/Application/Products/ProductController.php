<?php

declare(strict_types=1);

namespace App\Controller\Application\Products;

use Generator;
use App\Attribute\TurboFrame;
use App\Entity\Product\Product;
use App\Response\TurboRedirectResponse;
use App\Form\Entity\Product\ProductType;
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

    #[Route('/', name: 'app_products_list', methods: ['GET'])]
    public function index(#[TurboFrame] ?string $frame): Response
    {
        return $this->render('app/products/list.html.twig', [
            '_block' => $frame,
            'products' => $this->getProducts(),
        ]);
    }

    #[Route('/create', name: 'app_products_create', methods: ['GET', 'POST'])]
    public function create(#[TurboFrame] ?string $frame, Request $request): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->productRepository->persistAndFlush($data);

            return new TurboRedirectResponse($this->generateUrl('app_products_list'), $frame);
        }

        return $this->render('app/products/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'app_products_update', methods: ['GET', 'POST'])]
    public function update(#[TurboFrame] ?string $frame, Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->flush();

            return new TurboRedirectResponse($this->generateUrl('app_products_list'), $frame);
        }

        return $this->render('app/products/form.html.twig', [
            '_block' => $frame,
            'form' => $form,
        ]);
    }

    #[Route('/details/{id}', name: 'app_products_details', methods: ['GET', 'POST'])]
    public function details(Product $product, #[TurboFrame] ?string $frame): Response
    {
        return $this->render('app/products/details.html.twig', [
            '_block' => $frame,
            'product' => $product,
            'products' => $this->getProducts(),
        ]);
    }

    /**
     * @return Generator<array-key, Product>
     */
    private function getProducts(): Generator
    {
        yield from $this->productRepository->findAll();
    }
}
