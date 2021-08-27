<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Classe\Search;
use App\Form\SearchType;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        //Formulaire pour la recherche d'un produit ou en fonction des categories
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isvalid()){
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }else{
            //Afficher tous mes produits
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("produit/{slug}", name="product")
     */
    public function show($slug)
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        //dd($product);
        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1);//Récupére les meilleures vente de produits, que j'afficherais par la suite sur product/show.html.twig

        if(!$product){

            return $this->redirectToRoute('products');
        }

        return $this->render('product/show.html.twig', [
            'product'=> $product,
            'products' => $products
        ]);
    }




}
