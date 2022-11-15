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

#[Route('/forgotten-password')]
class ForgottenPasswordController extends AbstractController
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param TokenManager $tokenManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenManager           $tokenManager
    )
    {
    }

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
    #[Route(name: 'forgotten-password.index')]
    public function index(Request $request, Email $email, TranslatorInterface $translator): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('account.index', [
                'slug' => $this->getUser()->getSlug()
            ], Response::HTTP_MOVED_PERMANENTLY);
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

            if (!$user) {
                $this->addFlash('success', ucfirst($translator->trans('flash.forgotten_password.send.success')));

                return $this->redirectToRoute('security.index');
            }

            if (!$errors) {
                // Generate token for forgotten password
                $tokenManager = $this->tokenManager->checkAndSend(TOKEN::FORGOTTEN_PASSWORD, $user);
                // Send email for forgotten password
                $email->forgottenPassword($user, $tokenManager, $translator->trans('email.forgotten_password.subject'));
                // Flash user forgotten password link sent
                $this->addFlash('success', ucfirst($translator->trans('flash.forgotten_password.send.success')));

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
    #[Route('/{token}', name: 'forgotten-password.new')]
    public function new(
        Request                     $request,
        TranslatorInterface         $translator,
        string                      $token,
        UserPasswordHasherInterface $encoder): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('account.index', [
                'slug' => $this->getUser()->getSlug()
            ], Response::HTTP_MOVED_PERMANENTLY);
        }

        $now = new DateTime();

        $tokenPassword = $this->entityManager->getRepository(Token::class)
            ->findOneBy(
                [
                    'token' => $token,
                    'type'  => TOKEN::FORGOTTEN_PASSWORD
                ]
            );

        if (!$tokenPassword) {
            return $this->redirectToRoute('security.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        if ($now > $tokenPassword->getExpiredAt() || !$tokenPassword->getExpiredAt()) {
            $this->addFlash('error', ucfirst($translator->trans('flash.token.expired')));

            return $this->redirectToRoute('security.index', [], Response::HTTP_MOVED_PERMANENTLY);
        }

        $submittedToken = $request->request->get('_csrf_token');
        $submittedPassword = $request->request->get('password');

        $errors = [];

        if ('POST' === $request->getMethod()) {
            if (!$this->isCsrfTokenValid('forgotten_password_new', $submittedToken)) {
                $errors[] = $translator->trans('flash.csrf.invalid');
            } elseif (6 > strlen($submittedPassword)) {
                $errors[] = $translator->trans('user.password.length', [], 'validators');
            }

            if (!$errors) {
                $encoded = $encoder->hashPassword($tokenPassword->getUser(), $submittedPassword);

                $tokenPassword->getUser()
                    ->setPassword($encoded)
                    ->removeToken($tokenPassword);
                $this->entityManager->flush();
                // Flash user password changed
                $this->addFlash('success', ucfirst($translator->trans('flash.forgotten_password.new.success')));

                return $this->redirectToRoute('security.index');
            }
        }

        return $this->render('@front/security/forgotten_password.html.twig', [
            'errors' => $errors,
            'token'  => $token
        ]);
    }
}
