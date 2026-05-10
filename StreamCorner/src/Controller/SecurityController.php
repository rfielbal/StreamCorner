<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'login_error' => $error instanceof AuthenticationException ? $this->loginErrorMessage($error) : null,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    private function loginErrorMessage(AuthenticationException $error): string
    {
        $messageKey = $error->getMessageKey();
        $normalizedMessage = strtolower($messageKey);

        if (str_starts_with($messageKey, 'Veuillez ')) {
            return $messageKey;
        }

        if (str_contains($normalizedMessage, 'csrf')) {
            return 'Le formulaire de connexion a expiré. Veuillez réessayer.';
        }

        if (
            str_contains($normalizedMessage, 'bad credentials')
            || str_contains($normalizedMessage, 'invalid credentials')
            || str_contains($normalizedMessage, 'password is invalid')
            || str_contains($normalizedMessage, 'username could not be found')
        ) {
            return 'Email ou mot de passe incorrect.';
        }

        return 'Connexion impossible. Vérifiez vos informations puis réessayez.';
    }
}
