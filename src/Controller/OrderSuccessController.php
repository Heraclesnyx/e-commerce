<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart,$stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getuser() != $this->getUser()) //Evite qu'un user toto puisse accéder à la commande de l'user connecter
        {
            return $this->redirectToRoute('home');
        }

        if(!$order->getIsPaid())
        {
            //Vider la session "cart", pour vider le panier de l'utilisateur, il a acheté,il a fini sa commande donc on lui vide sa commande ensuite
            $cart->remove();

            //Modifier le status isPaid de notre commandeen mettant 1
            $order->setIsPaid(1); //On le met à 1
            $this->entityManager->flush(); //Et on le met en base

            //Envoyer un email a notre client pour lui confirmer sa commande
            $mail = new Mail();
            $content ='Bonjour ' . $order->getUser()->getFirstname().'<br/>Merci pour votre commande<br/><br/>Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(),'Votre commande sur E-commerce est bien validée.', $content);
        }

        //Afficher les qq infos de la commande de l'utilisateur
        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
