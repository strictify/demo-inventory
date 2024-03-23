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

class ProductDetailsController extends ProductCrudController
{
    #[Route('/transport/{id}', name: 'app_products_transport', methods: ['GET', 'POST'])]
    public function transport(Product $product): Response
    {
        return $this->render('app/products/details/transport.html.twig', [
            'product' => $product,
            'products' => $this->getProducts(),
        ]);
    }

    #[Route('/sales/{id}', name: 'app_products_sales', methods: ['GET', 'POST'])]
    public function sales(Product $product): Response
    {
        return $this->render('app/products/details/sales.html.twig', [
            'product' => $product,
            'products' => $this->getProducts(),
        ]);
    }
}