<?php

namespace App\Email;

use App\Entity\Event\Event;
use App\Entity\Token\Token;
use App\Entity\User\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class Email extends AbstractController
{
    public const ADMIN_NAME = 'Rôliste chaotique';
    public const ADMIN_EMAIL = 'noreply@roliste-chaotique.be';

    /**
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly MailerInterface     $mailer,
        private readonly TranslatorInterface $translator
    )
    {
    }

    /**
     * @param User|UserInterface $user
     * @param Token $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function emailVerify(User|UserInterface $user, Token $newToken, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/website/verify_email.html.twig')
            ->context([
                'subject' => $subject,
                'signedUrl' => $this->generateUrl('registration.verify-email', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param User $user
     * @param Token $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function forgottenPassword(User $user, Token $newToken, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/website/forgotten_password.html.twig')
            ->context([
                'subject' => $subject,
                'signedUrl' => $this->generateUrl('forgotten-password.new', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param Event $event
     * @param User $user
     *
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    public function eventWeekBefore(Event $event, User $user): void
    {
        $subject = $this->translator->trans('email.event_week_before.subject');

        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/website/event_week_before.html.twig')
            ->context([
                'subject' => $subject,
                'event' => $event->getName(),
                'user' => $user->getUsername(),
                'signedUrl' => $this->generateUrl('event.show', ['slug' => $event->getSlug()], 0),
            ]);
        $this->mailer->send($email);
    }
}

