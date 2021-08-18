<?php

namespace App\Controller;

use App\Classe\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach ($cart->getFull() as $product)
        {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(), //Récupère le prix du produit
                    'product_data' => [
                        'name' => $product['product']->getName(), //Récupère le nom du produit
                        'images' => [$YOUR_DOMAIN."/uploads/". $product['product']->getIllustration()], //Récupère l'illustration du produit
                    ],
                ],
                'quantity' => $product['quantity'], //Récupère la quantité du produit
            ];
        }

        Stripe::setApiKey('sk_test_51JPmu4JrnSsPZmVAoM6CkoxsWmFsaN8k7bpjaZweQniVQ0lvtKtpc10gGLpGtuD6KNE8QvMTBhYeI5LiHs8L3A5z00HKUUfr9D');

        $checkout_session = Session::create([
            'payment_method_types' => [
                'card',
            ],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response = new  JsonResponse(['id'=> $checkout_session->id]);
        return $response;

    }
}
