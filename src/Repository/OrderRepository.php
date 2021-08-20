<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /*
     * findSuccessOrders()
     * Permet d'afficher les commandes dans l'espace membre de l'utilisateur
     */
    public function findSuccessOrders($user)
    {
        return $this->createQueryBuilder('o') // o pour Order
            ->andWhere('o.isPaid = 1') //Commande finaliser
            ->andWhere('o.user = :user')
            ->setParameter('user', $user)
            ->orderBy('o.id', 'DESC') //Trier de manière descendant
            ->getQuery()
            ->getResult();
    }
}
