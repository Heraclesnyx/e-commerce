<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManger;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManger = $entityManager;
    }


    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        //Si l'user n'a pas d'adresse, je le renvoie à la page de création d'une adresse
        if(!$this->getUser()->getAdresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);


        return $this->render('order/index.html.twig',[
            'form' => $form->createView(),
            'cart' => $cart->getFull()  //getFull permet de récupérer toutes les infos du panier, se trouve dans src/Classe/Cart
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
//            dd($delivery);
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'. $delivery->getPhone();

            if($delivery->getCompany())
            {
                $delivery_content .= '<br/>'. $delivery->getCompany();
            }

            $delivery_content .= '<br/>'. $delivery->getAdress();
            $delivery_content .= '<br/>'. $delivery->getPostal(). ' '. $delivery->getCity();
            $delivery_content .= '<br/>'. $delivery->getCountry();
//            dd($delivery_content);

            //Enregistrer ma commande entity Order()
            $order = new Order();
            $order->setUser($this->getUser());

            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManger->persist($order);

            //Enregistrer mes produits entity OrderDetails()
            foreach ($cart->getFull() as $product)
            {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                $this->entityManger->persist($orderDetails);
            }

//            $this->entityManger->flush();//Ici on flush le tout càd Order et OrderDetails
            return $this->render('order/add.html.twig',[
                'cart' => $cart->getFull(),//getFull permet de récupérer toutes les infos du panier, se trouve dans src/Classe/Cart
                'carrier' => $carriers,
                'delivery' => $delivery_content
            ]);

        }
        return $this->redirectToRoute('cart'); //Renvoie à la page mon panier
    }
}
