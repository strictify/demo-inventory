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

class ProductCrudController extends AbstractController
{
    public function __construct(
        protected ProductRepository $productRepository,
    )
    {
    }

    #[Route('/', name: 'app_products_list', methods: ['GET'])]
    public function list(): Response
    {
        return $this->render('app/products/list.html.twig', [
            'products' => $this->getProducts(),
        ]);
    }

    #[Route('/create', name: 'app_products_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->productRepository->persistAndFlush($data);

            return $this->redirectToRoute('app_products_list');

//            return new TurboRedirectResponse($this->generateUrl('app_products_list'), 'main');
        }

        return $this->render('app/products/form.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: 'app_products_update', methods: ['GET', 'POST'])]
    public function update(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->productRepository->flush();

            return $this->redirectToRoute('app_products_list');
//            return new TurboRedirectResponse($this->generateUrl('app_products_list'), 'main');
        }

        return $this->render('app/products/form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @return Generator<array-key, Product>
     */
    protected function getProducts(): Generator
    {
        yield from $this->productRepository->findAll();
    }
}
