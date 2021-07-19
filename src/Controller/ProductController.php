<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/product", name="products")
     */
    public function index(): Response
    {
        //Afficher mes produits
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        //dd($products);

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }
}
