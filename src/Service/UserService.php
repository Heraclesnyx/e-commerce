<?php

namespace App\Service;


use Doctrine\ORM\EntityManagerInterface;

/**
 * Class UserService
 *
 * @package App\Service
 */
class UserService
{
    /**
     * @var ParamService $em
     */
    private $em;

    /**
     * @var EntityManagerInterface $entityManager
     */
    private $entityManager;

    public function __construct(ParamService $paramService, EntityManagerInterface $entityManager)
    {
        // Importer le Service.
        $this->em= $paramService;

        $this->entityManager = $entityManager;

    }

    /**
     * Remise à 0 du compte
     *
     * @param $user
     */
    public function resetAttemptSignInUser($user)
    {
        //Récupérer Doctrine
//        dd($user);
        //SetValue à 0 et flush()
        $user->setAttemptLogin(0);
        $this->entityManager->flush();

    }//end resetAttemptSignInUser()


}