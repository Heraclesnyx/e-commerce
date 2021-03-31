<?php

namespace App\Service;



use App\Entity\User;
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
     * Remise à 0 afin de débloquer le compte
     *
     * @param User $user
     * @return int
     */
    public function resetAttemptSignInUser(User $user) : int
    {
        //Récupérer Doctrine
        //SetValue à 0 et flush()
        $user->setAttemptLogin( 0);
        $this->entityManager->flush();

    }//end resetAttemptSignInUser()


}