<?php

declare(strict_types=1);

namespace App\Controller\Application\Products;

use App\Attribute\Page;
use Pagerfanta\Pagerfanta;
use App\Attribute\MainRequest;
use App\Entity\Product\Product;
use App\Turbo\Stream\ReplaceStream;
use App\Service\Mercure\StreamBuilder;
use App\Form\Entity\Product\ProductType;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\Product\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductCrudController extends AbstractController
{
    public function __construct(
        protected ProductRepository $productRepository,
        private FormFactoryInterface $formFactory,
    )
    {
    }


    #[Route('/filters', name: 'app_products_filters', methods: ['GET'])]
    public function filters(#[MainRequest] Request $request): Response
    {
        $filterForm = $this->formFactory->createNamed('filters', options: [
            'action' => $this->generateUrl('app_products_list'),
            'csrf_protection' => false,
            'method' => 'GET',
            'required' => false,
            'attr' => [
                'autocomplete' => 'off',
            ],
        ])
            ->add('name', options: [
                'required' => false,
            ]);
        $filterForm->handleRequest($request);

        return $this->renderBlock('app/products/list.html.twig', 'filters', [
            'form' => $filterForm,
        ]);
    }

    #[Route('/', name: 'app_products_list', methods: ['GET'])]
    public function list(#[Page] int $page): Response
    {
        return $this->render('app/products/list.html.twig', [
            'pager' => $this->getProducts($page),
        ]);
    }

    #[Route('/row/{id}', name: 'app_products_list_one', methods: ['GET'])]
    public function renderRow(Product $product, StreamBuilder $streamBuilder): Response
    {
        $html = $this->renderBlockView('app/products/list.html.twig', 'tr', [
            'product' => $product,
        ]);

        return $streamBuilder->createResponse(
            new ReplaceStream('product-' . $product->getId(), $html),
        );
    }

    #[Route('/create', name: 'app_products_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() && $data = $form->getData()) {
            $this->productRepository->persistAndFlush($data);

            return $this->redirectToRoute('app_products_list');
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

            return $this->redirectToRoute('app_products_list', ['_filters' => true], status: 303);
        }

        return $this->render('app/products/form.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @return Pagerfanta<Product>
     */
    protected function getProducts(int $page): Pagerfanta
    {
        return $this->productRepository->paginateWhere($page);
    }
}
