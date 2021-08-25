<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class AccountController
 *
 * @package App\Controller
 */
class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * Rendue de la vue du compte
     *
     * @Route("/account", name="account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * Changer le mot de passe dans son espace compte
     *
     * @param Request                      $request         Request.
     * @param UserPasswordEncoderInterface $encoder         pwd encoder.
     *
     * @return RedirectResponse|Response
     * @Route("/account/password", name="account_password")
    */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $notification = null;

        $user = $this->getUser(); //récupération d'un user
        $form = $this->createForm(ChangePasswordType::class, $user);


       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid()){
            $old_password= $form->get('old_password')->getData(); //récupération de l'ancien mot de passe.

           if($encoder->isPasswordValid($user, $old_password)){
                $new_password = $form->get('plainPassword')->getData(); //partie de l'enregistrement du nouveau password, plainPassword est dans resetPasswordType
                $password = $encoder->encodePassword($user, $new_password);
                $user->setPassword($password);
                $this->entityManager->flush();

                $notification = "Votre mot de passe à bien été modifier.";
            } else{
                $notification = "Votre mot de passe actuel n'est pas le bon.";
            }
           return $this->redirectToRoute('app_login');

        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }//Fin du reset password

}//Fin de la classe
