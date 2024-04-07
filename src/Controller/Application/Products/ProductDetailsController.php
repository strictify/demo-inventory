<?php

declare(strict_types=1);

namespace App\Controller\Application\Products;

use App\Attribute\Page;
use App\Entity\Product\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductDetailsController extends ProductCrudController
{
    #[Route('/transport/{id}', name: 'app_products_transport', methods: ['GET', 'POST'])]
    public function transport(Product $product, #[Page] int $page): Response
    {
        return $this->render('app/products/details/transport.html.twig', [
            'product' => $product,
            'pager' => $this->getProducts($page),
        ]);
    }

    #[Route('/sales/{id}', name: 'app_products_sales', methods: ['GET', 'POST'])]
    public function sales(Product $product, #[Page] int $page): Response
    {
        return $this->render('app/products/details/sales.html.twig', [
            'product' => $product,
            'pager' => $this->getProducts($page),
        ]);
    }
}
