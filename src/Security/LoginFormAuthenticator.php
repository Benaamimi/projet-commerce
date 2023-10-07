<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    protected $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $request->request->all()['login'];
        return new Passport(
            new UserBadge($credentials['email']),
            new PasswordCredentials($credentials['password'])
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse('/');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // $errorMessage = "Erreur d'authentification";

        if ($exception->getMessage() === "Bad credentials.") {
            $errorMessage = "Cette mail n'est pas connue";
        } elseif ($exception->getMessage() === "The presented password is invalid.") {
            $errorMessage = "Le mot de passe est incorrect";
        }
        $exception = new AuthenticationException($errorMessage);


        $request->getSession()->set(Security::LAST_USERNAME, $request->request->all()['login']['email']);
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->generator->generate('security_login'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        
        return new RedirectResponse('/login');
    }

    
   
}
