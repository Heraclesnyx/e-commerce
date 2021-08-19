<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager,Cart $cart, $reference): Response
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        
        if(!$order)
        {
            new  JsonResponse(['error'=> 'order']);
        }

        //On récupère les produits pour stripe au moment de payer
        foreach ($order->getorderDetails()->getValues() as $product)
        {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(), //Récupère le prix du produit
                    'product_data' => [
                        'name' => $product->getProduct(), //Récupère le nom du produit dans orderDetails
                        'images' => [$YOUR_DOMAIN."/uploads/". $product_object->getIllustration()], //Récupère l'illustration du produit
                    ],
                ],
                'quantity' => $product->getQuantity(), //Récupère la quantité du produit
            ];
        }

        //Récupération du transporteur pour stripe
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],//Ici mettre une icone pour transporteur
                ],
            ],
            'quantity' => 1, //Toujours 1 transporteur
        ];

        Stripe::setApiKey('sk_test_51JPmu4JrnSsPZmVAoM6CkoxsWmFsaN8k7bpjaZweQniVQ0lvtKtpc10gGLpGtuD6KNE8QvMTBhYeI5LiHs8L3A5z00HKUUfr9D');

        $checkout_session = Session::create([
            'customer_email'=> $this->getUser()->getEmail(), //Permet de ne plus renseigner son adresse mail lors du payement sur l'api de stripe
            'payment_method_types' => [
                'card',
            ],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $response = new  JsonResponse(['id'=> $checkout_session->id]);
        return $response;

    }
}
