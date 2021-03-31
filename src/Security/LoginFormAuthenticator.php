<?php

namespace App\Security;

use App\Entity\User;
use App\Service\ParamService;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
//use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LoginFormAuthenticator
 *
 * @package App\Security
 */
class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    /**
     * Entity manager
     *
     * @var EntityManagerInterface  Entity.
     */
    private $entityManager;

    /**
     * Url
     *
     * @var UrlGeneratorInterface Url.
     */
    private $urlGenerator;

    /**
     * Csrf
     *
     * @var CsrfTokenManagerInterface Csrf.
     */
    private $csrfTokenManager;

    /**
     * User
     *
     * @var UserPasswordEncoderInterface User.
     */
    private $passwordEncoder;

    /**
     * @var ParamService
     */
    private $paramService;

    /**
     * @var
     */
    private $userService;


    /**
     * LoginFormAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager Entity.
     * @param UrlGeneratorInterface $urlGenerator Url.
     * @param CsrfTokenManagerInterface $csrfTokenManager Csrf.
     * @param UserPasswordEncoderInterface $passwordEncoder User pwd encoder.
     *
     */
    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder, ParamService $paramService, UserService $userService)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->paramService = $paramService;
        $this->userService = $userService;
    }


    /**
     * Request
     *
     * @param Request $request Request.
     *
     * @return boolean
     */
    public function supports(Request $request)
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }//fin de support()


    /**
     * Get credentials
     *
     * @param Request $request Request.
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

//      dd($credentials);

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }//fin de getCredentials()



    /**
     * Get current user
     *
     * @param mixed                 $credentials  Credentials.
     * @param UserProviderInterface $userProvider User.
     *
     *
     * @throws InvalidCsrfTokenException Csrf invalid.
     * @throws CustomUserMessageAuthenticationException Custom error msg.
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);
        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Email could not be found.');
        }

        return $user;
    }//fin getUser()


    /**
     * Check credentials
     *
     * @param mixed         $credentials Credentials.
     * @param UserInterface $user        User.
     *
     * @return boolean
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
//        dd($user);
        /* Old code */
       // return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);

        /* New code */
        // On vérifie si le mot de passe est valide.
        $successLogin = $this->passwordEncoder->isPasswordValid($user, $credentials['email']);

        // S'il n'est pas valide, on regarde si la limite de tentative de connexion est activée (0 = non, x = oui avec x tentatives max).

        $tentative = $this->$user->getAttemptLogin(); //reucpérer le nbr de tentative autorisé
        if(!$successLogin)
        {

            return !$tentative ? false : $tentative->getAttemptLogin();
        }


//        // Si le nombre de tentatives est inférieur au nombre autorisé, on rajoute +1 au nombre et on met à jour l'utilisateur à jour.
        if($tentative < $this->paramService->getLoginAttempt()){ //Nbr tentative est dans user $attemptLogin, nbr autorisé est dans parameters code = Signin_attempt et value = 3
            $compteur = $this->$user->getCompteur(); //Récuperer le compteur de user et l'incrémenter ensuite
            $compteur ++;

            $user->setCompteur($compteur);  //je set le compteur avec la valeur incrémenter et j'enregistre en base
            $this->entityManager->flush();
        }


        // Si le nombre de tentatives est supérieur au nombre autorisé, on bloque l'utilisateur, on notifie par mail le propriétaire du compte pour qu'il débloque le compte via un code à saisir.
        if($tentative  > $this->paramService->getLoginAttempt()){
            $this->$user->setIsActive(false); //Desactivation du compte
        }


        return $successLogin;

    } //Fin de checkCredentials()


    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param array $credentials Credentials.
     *
     * @return string|null
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];

    }//Fin de getPassword()


    /**
     * Auth success
     *
     * @param Request $request Request.
     * @param TokenInterface $token Token.
     * @param string $providerKey String.
     * @return RedirectResponse|null
     * @throws \Exception Exception.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
//        dd($token);
        // On remet à zéro le nombre de tentative de connexion de l'utilisateur (il est dans user attemptlogin() -> méthode resetAttemptSignInUser du UserService).
        // Puis on sauvegarde en base.
        try {
             $this->userService->resetAttemptSignInUser($token->getUser()); //appel De UserService
        }catch (\Exception $e){
            dump($e->getMessage());die();
        }

        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

         return new RedirectResponse($this->urlGenerator->generate('account'));
    }//Fin de onAuthenticationSuccess()



    /**
     * Get login url
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }//Fin de getLoginUrl()

}//Fin de la classe
