<?php

namespace App\Controller\Front\Security;

use App\Email\Email;
use App\Entity\Token\Token;
use App\Entity\User\User;
use App\Service\TokenManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgottenPasswordController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenManager $tokenManager
    ) { }

    /**
     * @param Request $request
     * @param Email $email
     * @param TranslatorInterface $translator
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    #[Route('/forgotten-password', name: 'forgotten-password.index')]
    public function index(Request $request, Email $email, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index', ['slug' => $this->getUser()->getSlug()]);
        }

        $submittedToken = $request->request->get('_csrf_token');
        $submittedEmail = $request->request->get('email');

        $errors = [];

        if ('POST' === $request->getMethod()) {
            if (!$this->isCsrfTokenValid('forgotten_password', $submittedToken)) {
                $errors[] = $translator->trans('flash.csrf.invalid');
            }

            $repository = $this->entityManager->getRepository(User::class);
            $user = $repository->findOneBy(['email' => $submittedEmail]);

            if (!$user) {$errors[] = ucfirst($translator->trans('flash.email.not_found'));}

            if (!$errors) {
                // Set an event to inform the app a token is sent
                $tokenManager = $this->tokenManager->checkAndSend(TOKEN::FORGOTTEN_PASSWORD, $user);

                $email->forgottenPassword($user, $tokenManager, $translator->trans('email.forgotten_password.subject'));
                $this->addFlash('success', $translator->trans('flash.forgotten_password.send.success'));

                return $this->redirectToRoute('security.index');
            }
        }

        return $this->render('@front/security/forgotten_password.html.twig', [
            'errors' => $errors
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param string $token
     * @param UserPasswordHasherInterface $encoder
     *
     * @return Response
     */
    #[Route('/forgotten-password/{token}', name: 'forgotten-password.new')]
    public function new(
        Request $request,
        TranslatorInterface $translator,
        string $token,
        UserPasswordHasherInterface $encoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('account.index', ['slug' => $this->getUser()->getSlug()]);
        }

        $now = new DateTime();

        $tokenRepository = $this->entityManager->getRepository(Token::class);
        $tokenPassword = $tokenRepository->findOneBy(
            [
                'token' => $token,
                'type' => TOKEN::FORGOTTEN_PASSWORD
            ]
        );

        if (!$tokenPassword) {
            $this->addFlash('error', $translator->trans('flash.token.invalid'));

            return $this->redirectToRoute('security.index');
        }

        if ($now > $tokenPassword->getExpiredAt() || !$tokenPassword->getExpiredAt()) {
            $this->addFlash('error', $translator->trans('flash.token.expired'));

            return $this->redirectToRoute('security.index');
        }

        $submittedToken = $request->request->get('_csrf_token');
        $submittedPassword = $request->request->get('password');

        $errors = [];

        if ('POST' === $request->getMethod()) {
            if (!$this->isCsrfTokenValid('forgotten_password_new', $submittedToken)) {
                $errors[] = $translator->trans('flash.csrf.invalid');
            } elseif (6 > strlen($submittedPassword)) {
                $errors[] = 'Le mot de passe doit faire au minimum 6 caractères';
            }

            if (!$errors) {
                $encoded = $encoder->hashPassword($tokenPassword->getUser(), $submittedPassword);

                $tokenPassword->getUser()->setPassword($encoded);
                $tokenPassword->getUser()->removeToken($tokenPassword);
                $this->entityManager->flush();

                $this->addFlash('success', $translator->trans('flash.forgotten_password.new.success'));

                return $this->redirectToRoute('security.index');
            }
        }

        return $this->render('@front/security/forgotten_password.html.twig', [
            'errors' => $errors,
            'token' => $token
        ]);
    }
}
