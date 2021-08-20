<?php

namespace App\Repository;

use App\Entity\Parameters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parameters|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parameters|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parameters[]    findAll()
 * @method Parameters[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParametersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parameters::class);
    }

    /**
     * Retourne la valeur d'un paramètre grâce à son code.
     *
     * @param string $code Le code du paramètre.
     *
     * @return string|null
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getParamByCode (string $code): ?Parameters {

        $qb = $this->createQueryBuilder('c')
            ->where('c.code = :CODE')
            ->setParameter('CODE', $code);

        return $qb->getQuery()->getOneOrNullResult();

    }
}
