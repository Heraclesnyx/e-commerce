<?php

//Gestion pour le panier avec les quantité et les sessions lié au panier


namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    //Ajouter des produits à mon panier
    public function add($id)
    {
        $cart = $this->session->get('cart', []); //Récupération du contenu de cart

        if(!empty($cart[$id]))
        {
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }


    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }


    public function delete($id)
    {
        $cart = $this->session->get('cart', []); //Récupération du contenu de cart
        unset($cart[$id]); //Enlève du tableau le produit dont l'id est supprimer

        return $this->session->set('cart', $cart);
    }

    //Vérification de la quantité de notre produit si il n'est pas = 1, si =1 faut supprimer notre produit
    public function decrease($id)
    {
        $cart = $this->session->get('cart', []); //Récupération du contenu de cart

        if($cart[$id] > 1){
            //Retire 1 produit(ou quantité)
            $cart[$id]--;
        }else{
            //Supprime le produit
            unset($cart[$id]);
        }
        return $this->session->set('cart', $cart);
    }

    //Récupération de tout mon panier
    public function getFull()
    {
        $panierComplet = [];

        if($this->get()) {
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneBy(['id' => $id]);

                //Permet d'éviter de rentrer un id à la main qui n'existe pas afin d'éviter de faire planter mon appli
                if(!$product_object){
                    $this->delete($id);
                    continue; //Sortie de la boucle foreach
                }

                $panierComplet[] = [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $panierComplet;
    }
}