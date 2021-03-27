<?php

namespace App\Service;



use App\Repository\ParametersRepository;
//use phpDocumentor\Reflection\Types\Boolean;
use PhpParser\Builder\Param;


/**
 * Class ParamService
 *
 * @package App\Service
 */
class ParamService
{
    private $em;

    public function __construct(ParametersRepository $repository)
    {
        //importer le repository
        $this->em= $repository;
//        $repository= $em->getRepository(ParamService::class);    Importation d'un repository
    }

    public function getLoginAttempt(string $code): ?Param
    {
        //Vérification si la ligne existe en premier(return 0)

        return $this->em->checkCodeParameters($code);

//        dd($this->em->checkCodeParameters($code));
        //Nombre d'essai
//        return (int)$check;
    }


//    public function isEmailVerificationnabled(): Boolean{
//        //Vérification si la ligne existe en premier(return false)
//
//        //Vérification active ou non
//        return (bool)(int)$mavaleur;
//    }
}