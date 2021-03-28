<?php

namespace App\Controller;

use App\Service\ParamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * Login form
     *
     * @param AuthenticationUtils $authenticationUtils Authentication utils.
     *
     * @Route("/login", name="app_login")
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('account');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }//Fin de la function login()

    /**
     * Logout
     *
     * @Route("/logout", name="app_logout")
     *
     * @return RedirectResponse
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }//Fin de la function logout()

    /**
     * @Route("/hello", name="hello")
     *
     * @return Response
    */
    public function test(ParamService $paramService, int $code = 3): Response
    {
        if($paramService->getLoginAttempt($code)){
            $this->addFlash('success', 'Bienvenue');
        }
    }

}//Fin de la classe
