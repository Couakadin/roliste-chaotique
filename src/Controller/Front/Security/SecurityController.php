<?php

namespace App\Controller\Front\Security;

use DateTime;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security.index')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('@front/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'security.logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/terms-and-conditions', name: 'security.terms-and-conditions')]
    public function termsAndConditions(): Response
    {
        return $this->render('@front/security/terms_and_conditions.html.twig');
    }

    #[Route('/privacy-policy', name: 'security.privacy-policy')]
    public function privacyPolicy(): Response
    {
        return $this->render('@front/security/privacy_policy.html.twig');
    }

    #[Route('/cookie-policy', name: 'security.cookie-policy')]
    public function cookiePolicy(Request $request): Response
    {
        $data = $request->query->get('cookie');
        $cookie = $request->cookies->get('cookie');

        if ($data && !$cookie) {
            $cookie = Cookie::create('cookie')
                ->withValue('true')
                ->withExpires(new DateTime('+ 1 year'));

            $response = new Response('cookie settings', 200);
            $response->headers->setCookie($cookie);

            return $response;
        }

        return $this->render('@front/security/cookie_policy.html.twig');
    }
}
