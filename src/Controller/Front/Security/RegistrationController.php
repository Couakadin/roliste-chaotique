<?php

namespace App\Controller\Front\Security;

use App\Email\Email;
use App\Email\EmailAdmin;
use App\Entity\Token\Token;
use App\Entity\User\User;
use App\Form\Security\RegistrationFormType;
use App\Service\BadgeManager;
use App\Service\TokenManager;
use DateTimeImmutable;
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

#[Route('/register')]
class RegistrationController extends AbstractController
{
    /**
     * @param TranslatorInterface $translator
     * @param UserAuthenticatorInterface $userAuthenticator
     * @param FormLoginAuthenticator $formLoginAuthenticator
     * @param Email $email
     * @param EmailAdmin $emailAdmin
     * @param EntityManagerInterface $entityManager
     * @param BadgeManager $badgeManager
     * @param TokenManager $tokenManager
     */
    public function __construct(
        private readonly TranslatorInterface        $translator,
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly FormLoginAuthenticator     $formLoginAuthenticator,
        private readonly Email                      $email,
        private readonly EmailAdmin                 $emailAdmin,
        private readonly EntityManagerInterface     $entityManager,
        private readonly BadgeManager               $badgeManager,
        private readonly TokenManager               $tokenManager
    )
    {
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     *
     * @return RedirectResponse|Response|null
     *
     * @throws TransportExceptionInterface
     */
    #[Route(name: 'registration.register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): RedirectResponse|Response|null
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('account.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Register the new user
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // User is now logged in
            $user->setLoggedAt(new DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            // Unlock badge
            $this->badgeManager->checkAndUnlock($user, 'register', 1);
            // Generate token to verify email
            $tokenManager = $this->tokenManager->checkAndSend(TOKEN::EMAIL_VERIFY, $user);
            // Send email to verify email
            $this->email->emailVerify($user, $tokenManager, $this->translator->trans('email.verify_email.subject'));
            // Inform admin of a new inscription
            $this->emailAdmin->inscriptionAdmin($user);
            // Flash user register confirmation
            $this->addFlash('success', ucfirst($this->translator->trans('flash.register.success', ['%user%' => $user->getUsername()])));

            return $this->userAuthenticator->authenticateUser($user, $this->formLoginAuthenticator, $request);
        }

        return $this->render('@front/security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    #[Route('/verify-email', name: 'registration.verify-email')]
    public function verifyEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        $now = new DateTimeImmutable('now');

        $token = $this->entityManager->getRepository(Token::class)
            ->findOneBy(
                [
                    'user'  => $this->getUser(),
                    'token' => $request->query->get('token'),
                    'type'  => TOKEN::EMAIL_VERIFY
                ]
            );

        if (!$token || $this->getUser()->isVerified()) {
            return $this->redirectToRoute('security.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        if ($now > $token->getExpiredAt() || !$token->getExpiredAt()) {
            $this->addFlash('error', ucfirst($this->translator->trans('flash.token.expired')));

            return $this->redirectToRoute('security.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        $this->getUser()->setIsVerified(true);
        $token->getUser()->removeToken($token);

        $this->entityManager->flush();

        // Flash user email successful verified
        $this->addFlash('success', ucfirst($this->translator->trans('flash.token.success_email')));

        return $this->redirectToRoute('account.edit', ['slug' => $this->getUser()->getSlug()]);
    }


    /**
     * @return Response
     *
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    #[Route('/verify-resend', name: 'registration.resend-verify-email')]
    public function resendVerifyEmail(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        if ($this->getUser()->isVerified()) {
            return $this->redirectToRoute('account.edit', [
                'slug' => $this->getUser()->getSlug()
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        // Generate token to verify email
        $tokenManager = $this->tokenManager->checkAndSend(TOKEN::EMAIL_VERIFY, $this->getUser());
        // Send email to verify email
        $this->email->emailVerify($this->getUser(), $tokenManager, $this->translator->trans('email.verify_email.subject'));
        // Flash user email verification sent
        $this->addFlash('success', ucfirst($this->translator->trans('flash.email.verify_sent')));

        return $this->redirectToRoute('account.edit', ['slug' => $this->getUser()->getSlug()]);
    }
}
