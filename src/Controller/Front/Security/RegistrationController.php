<?php

namespace App\Controller\Front\Security;

use App\Email\Email;
use App\Email\EmailAdmin;
use App\Entity\Token\Token;
use App\Entity\User\User;
use App\Form\Security\RegistrationFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    private TranslatorInterface        $translator;
    private UserAuthenticatorInterface $userAuthenticator;
    private FormLoginAuthenticator     $formLoginAuthenticator;
    private Email                      $email;
    private EmailAdmin                 $emailAdmin;
    private EntityManagerInterface     $entityManager;

    public function __construct(
        TranslatorInterface        $translator,
        UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator,
        Email                      $email,
        EmailAdmin                 $emailAdmin,
        EntityManagerInterface     $entityManager
    )
    {
        $this->translator = $translator;
        $this->userAuthenticator = $userAuthenticator;
        $this->formLoginAuthenticator = $formLoginAuthenticator;
        $this->email = $email;
        $this->emailAdmin = $emailAdmin;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/register", name="security.registration.register")
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): RedirectResponse|Response|null
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front.account.index');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setLoginAt(new DateTime());

            $newToken = new Token($user, Token::EMAIL_VERIFY);

            $entityManager->persist($user);
            $entityManager->persist($newToken);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->email->emailVerify($user, $newToken, $this->translator->trans('email.new_inscription.subject'));
            $this->emailAdmin->emailNewInscriptionAdmin($user);

            $this->addFlash('success', $this->translator->trans('flash.register.success', ['%user%' => $user->getUsername()]));

            return $this->userAuthenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
        }

        return $this->render('@front/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="security.registration.verify_user_email")
     */
    public function verifyEmail(Request $request): Response
    {
        if (!$this->getUser()) {
            $this->addFlash('warning', $this->translator->trans('flash.verify_email.not_login'));

            return $this->redirectToRoute('front.home.index');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $now = new DateTime('now');

        $tokenRepository = $this->entityManager->getRepository('App:Token\Token');
        $token = $tokenRepository->findOneBy(['token' => $request->query->get('token')]);

        if (!$token || $this->getUser()->isVerified()) {
            $this->addFlash('error', $this->translator->trans('flash.token.invalid'));

            return $this->redirectToRoute('front.home.index');
        } elseif ($now > $token->getExpiredAt() || !$token->getExpiredAt()) {
            $this->addFlash('error', $this->translator->trans('flash.token.expired'));

            return $this->redirectToRoute('front.home.index');
        }

        $verified = $this->getUser();
        $verified->setIsVerified(true);
        $verified->removeToken($token);

        $this->entityManager->persist($verified);
        $this->entityManager->remove($token);
        $this->entityManager->flush();

        $this->addFlash('success', ucfirst($this->translator->trans('flash.token.success_email')));

        return $this->redirectToRoute('front.home.index');
    }


    /**
     * @Route("/verify/resend", name="security.registration.verify_resend_email")
     */
    public function resendVerifyEmail()
    {
        return $this->render('@front/security/resend_verify_email.html.twig');
    }
}
