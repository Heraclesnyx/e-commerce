<?php

namespace App\Controller;

use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account", name="account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/account/password", name="account_password")
    */
    public function resetPassword(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $old_password= $form->get('old_password')->getData();

            if($encoder->isPasswordValid($user, $old_password)){

                $new_password = $form->get('new_password')->getData();
                $password = $encoder->encodePassword($user, $new_password);

                $user->setPassword($password);
                $this->entityManager->flush();

                $notification = "Votre mot de passe à bien été modifier.";
            }else{
                $notification = "Votre mot de passe actuel n'est pas le bon.";
            }
//            return $this->redirectToRoute('account');

        }

        return $this->render('account/reset_password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }

}
