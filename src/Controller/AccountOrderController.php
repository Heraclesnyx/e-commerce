<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    private $entityManer;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManer = $entityManager;
    }


    /**
     * @Route("/account/order", name="account_order")
     */
    public function index(): Response
    {
        $orders = $this->entityManer->getRepository(Order::class)->findSuccessOrders($this->getUser());

        return $this->render('account/order.html.twig',[
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/order/{reference}", name="account_order_show")
     */
    public function show($reference): Response
    {
        $order = $this->entityManer->getRepository(Order::class)->findOneByReference($reference);

        if(!$order || $order->getuser() != $this->getUser())
        {
            return $this->redirectToRoute('account_order');
        }

        return $this->render('account/order_show.html.twig',[
            'order' => $order
        ]);
    }
}
