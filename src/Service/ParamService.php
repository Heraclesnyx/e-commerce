<?php

namespace App\Service;


use App\Repository\ParametersRepository;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Class ParamService
 *
 * @package App\Service
 */
class ParamService
{
    /**
     * @var ParametersRepository $em
     */
    private $em;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    /**
     * ParamService constructor.
     * @param ParametersRepository $repository
     */
    public function __construct(ParametersRepository $repository, EntityManagerInterface $entityManager)
    {
        // Importer le repository.
        $this->em= $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * Retourne le nombre de tentatives autorisées pour se connecter.
     *
     * @return integer
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLoginAttempt(): int
    {
        // Récupération du nombre de tentative de connexion.
        $nbr = $this->em->getParamByCode('SIGNIN__ATTEMPT');

        // Retourne le nombre de tentatives autorisées, 0 si le paramètre n'existe pas.
        return !$nbr ? 0 : (int) $nbr->getValue();
    }//end getLoginAttempt()


    /**
     * Retourne si la validationn de compte par e-mail est activée.
     *
     * @return boolean
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isEmailVerificationnabled(): bool {
        // Vérification si la ligne existe en premier(return false)
         $email = $this->em->getParamByCode('EMAIL__VALIDATION');

        // Vérification active ou non
        return !$email ? false : (bool)(int)$email->getValue(); //(bool)(int) == double cast => signifie ici que le int retournera un booleen

    }//end isEmailVerificationnabled()
}