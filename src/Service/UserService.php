<?php

namespace App\Service;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;


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


    public function resetAttemptSignInUser($user)
    {
        //Récupérer Doctrine
//        dd($user);
        //SetValue à 0 et flush()
        $user->setAttemptLogin(0);
        $this->entityManager->flush();

    }//end resetAttemptSignInUser()


}