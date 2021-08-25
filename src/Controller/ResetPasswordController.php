<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/reset/password", name="reset_password")
     */
    public function index(Request $request): Response
    {
        //Si mon utilisateur est déjà connecter et logger, je le redirige vers la home
        if($this->getUser())
        {
            return $this->redirectToRoute('home');
        }

        if($request->get('email'))
        {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user)
            {
                //Enregistrer en base la demande de reset d'un password avec user,token et createdAt
                $reset_password = new  ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());

                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                //Envoyer un mail à l'user avec un lien lui permettant de changer son mot de passe
                $url =$this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ". $user->getFirstname(). ",". "<br/>Vous avez demander de réinitialiser votre mot de passe<br/><br/>";
                $content .= "Merci de bien cliquer sur le lien afin de <a href='".$url."'>réinitialiser votre mot de passe.</a>";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname(). ' '.$user->getLastname(), 'Réinitialiser le mot de passe', $content);

                $this->addFlash('notice', 'Vous allez recevoir un mail pour réinitialiser votre mot de passe.');
            }else{
                $this->addFlash('notice', 'Cette adresse mail est inconnue.');

            }
        }


        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/reset/update/{token}", name="update_password")
     */
    public function update(Request $request,$token, UserPasswordEncoderInterface $encoder)
    {
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if(!$reset_password)
        {
            return $this->redirectToRoute('reset_password');
        }

        //Verifier si createdAt == now - 10h
        $now = new  \DateTime();

        if($now > $reset_password->getCreatedAt()->modify('+ 10 hour')) {
            $this->addFlash('notice', 'Votre demande de mot de passe est expiré. Veuillez la renouveller.');

            return $this->redirectToRoute('reset_password');
        }

        //Rendre vue ac mot de passe et confirmation du mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $new_pwd = $form->get('new_password')->getData();

            //Encoder les mots de passe
            $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);//On récupére le user ainsi que le nouveau mot de passe à encoder
            $reset_password->getUser()->setPassword($password);

            //Flush en bdd
            $this->entityManager->flush();

            //Redirection de l'user vers la page de login
            $this->addFlash('notice', 'Votre mot de passe à bien été mise à jour.');

            return $this->redirectToRoute('app_login'); //redirection vers la page de login
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
