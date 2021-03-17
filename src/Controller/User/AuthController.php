<?php

namespace App\Controller\User;

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
     * @param UserPasswordEncoderInterface $passwordEncoder User pwd encoder.
     *
     * @return
     *
     * @Route("/register", name="user_register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            try{
                $password = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

            }catch (\Exception $e){
                dump($e->getMessage());die();
            }

            return $this->redirectToRoute('home');
        }

        return $this->render('auth/index.html.twig',[
            'form'=> $form->createView()
        ]);
    }//Fin de la function index()
}//Fin de la classe
