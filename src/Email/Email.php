<?php

namespace App\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Email extends AbstractController
{
    private const ADMIN_NAME = 'Rôliste chaotique';
    private const ADMIN_EMAIL = 'contact@roliste-chaotique.be';

    /**
     * @param MailerInterface $mailer
     */
    public function __construct(private readonly MailerInterface $mailer) { }

    /**
     * @param $user
     * @param $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function emailVerify($user, $newToken, string $subject)
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/verify_email.html.twig')
            ->context([
                'subject'   => $subject,
                'signedUrl' => $this->generateUrl('registration.verify-email', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param $user
     * @param $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function forgottenPassword($user, $newToken, string $subject)
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/forgotten_password.html.twig')
            ->context([
                'subject' => $subject,
                'signedUrl' => $this->generateUrl('forgotten-password.new', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }
}

