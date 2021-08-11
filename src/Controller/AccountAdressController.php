<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAdressController extends AbstractController
{
    /**
     * @Route("/account/address", name="account_address")
     */
    public function index(): Response
    {
        dd($this->getUser());
        return $this->render('account/address.html.twig');
    }
}
