<?php

namespace App\Controller\User;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * Class AuthController
 *
 * @package App\Controller
 */
class AuthController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    //* @param MailerService                $mailerService   Send mail.
    //* @param TokenGeneratorInterface      $tokenGenerator  Token.
    /**
     * Register a new User
     *
     * @param Request                      $request         Request.
     * @param UserPasswordEncoderInterface $encoder User pwd encoder.
     *
     * @return Response
     *
     * @Route("/register", name="user_register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email)
            {
//                try{

                    $password = $encoder->encodePassword($user, $user->getPlainPassword()); //On récupére plainPassword et non Password (sinon sa casse tout)
                    $user->setPassword($password);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    $mail = new Mail();
                    $content ="Bonjour " . $user->getFirstname()."<br/>Bienvenue sur mon 1er site de jeux vidéos<br/><br/>Lorem Ipsum is simply dummy text of the printing and typesetting industry.";
                    $mail->send($user->getEmail(), $user->getFirstname(),"Bienvenue sur mon premier site E-commerce.", $content);

                    $notification = "Votre isncription s'est bien déroulé. Vous pouvez dès maintenant vous connecter à votre comtpe";

//                }catch (\Exception $e){
//                    dump($e->getMessage());die();
//                }
            }else{
                $notification = 'L\'email que vous avez entrer est déjà existante';
            }


           return $this->redirectToRoute('home');
        }

        return $this->render('auth/index.html.twig',[
            'form'=> $form->createView(),
            'notification' => $notification
        ]);
    }//Fin de la function index()
}//Fin de la classe
