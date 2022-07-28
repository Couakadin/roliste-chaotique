<?php

namespace App\Controller\Front\Security;

use App\Email\Email;
use App\Entity\Token\Token;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForgottenPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/forgotten_password", name="security.forgotten_password.index")
     * @param Request $request
     * @param Email $email
     * @param TranslatorInterface $translator
     * @return Response
     * @throws Exception|TransportExceptionInterface|\Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function index(Request $request, Email $email, TranslatorInterface $translator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front.account.index', ['slug' => $this->getUser()->getSlug()]);
        }

        $submittedToken = $request->request->get('_csrf_token');
        $submittedEmail = $request->request->get('email');

        $errors = [];

        if ('POST' === $request->getMethod()) {
            if (!$this->isCsrfTokenValid('forgotten_password', $submittedToken)) {
                $errors[] = $translator->trans('flash.csrf.invalid');
            }

            $repository = $this->entityManager->getRepository('App:User\User');
            $user = $repository->findOneBy(['email' => $submittedEmail]);

            if (!$user) {$errors[] = ucfirst($translator->trans('flash.email.not_found'));}

            if (!$errors) {
                $token = new Token($user, 'forgotten_password');

                $this->entityManager->persist($token);
                $this->entityManager->flush();

                $email->forgottenPassword($user, $token, $translator->trans('email.forgotten_password.subject'));

                $this->addFlash('success', $translator->trans('flash.forgotten_password.send.success'));

                return $this->redirectToRoute('front.home.index');
            }
        }

        return $this->render('@front/security/forgotten_password.html.twig', [
            'errors' => $errors
        ]);
    }

    /**
     * @Route("/forgotten_password/{token}", name="front.home.forgotten_password.new")
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param string $token
     * @param UserPasswordHasherInterface $encoder
     * @return Response
     */
    public function new(
        Request $request,
        TranslatorInterface $translator,
        string $token,
        UserPasswordHasherInterface $encoder): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('front.account.index', ['slug' => $this->getUser()->getSlug()]);
        }

        $now = new DateTime();

        $tokenRepository = $this->entityManager->getRepository('App:Token\Token');
        $token = $tokenRepository->findOneBy(['token' => $token]);

        if (!$token) {
            $this->addFlash('error', $translator->trans('flash.token.invalid'));

            return $this->redirectToRoute('front.home.index');
        } elseif ($now > $token->getExpiredAt() || !$token->getExpiredAt()) {
            $this->addFlash('error', $translator->trans('flash.token.expired'));

            return $this->redirectToRoute('front.home.index');
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
                $encoded = $encoder->hashPassword($token->getUser(), $submittedPassword);

                $token->getUser()->setPassword($encoded);
                $this->entityManager->remove($token);
                $token->getUser()->removeToken($token);
                $this->entityManager->flush();

                $this->addFlash('success', $translator->trans('flash.forgotten_password.new.success'));

                return $this->redirectToRoute('security.login.index');
            }
        }

        return $this->render('@front/security/forgotten_password.html.twig', [
            'errors' => $errors,
            'token' => $token
        ]);
    }
}