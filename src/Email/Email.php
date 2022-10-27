<?php

namespace App\Email;

use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Token\Token;
use App\Entity\User\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class Email extends AbstractController
{
    private const ADMIN_NAME = 'Rôliste chaotique';
    private const ADMIN_EMAIL = 'contact@roliste-chaotique.be';

    public function __construct(
        private readonly MailerInterface     $mailer,
        private readonly TranslatorInterface $translator,
    )
    {
    }

    /**
     * @param User $user
     * @param Token $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function emailVerify(User $user, Token $newToken, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/website/verify_email.html.twig')
            ->context([
                'subject'   => $subject,
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
                'subject'   => $subject,
                'signedUrl' => $this->generateUrl('forgotten-password.new', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function newTableFavorite(User $user, Table $table, Event $event, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($this->translator->trans($subject, ['%table%' => $table])))
            ->htmlTemplate('@email/website/new_table_favorite.html.twig')
            ->context([
                'subject'   => $subject,
                'table'     => $table,
                'user'      => $user,
                'event'     => $event,
                'signedUrl' => $this->generateUrl('event.show', ['slug' => $event->getSlug()], 0),
            ]);
        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function editTableParticipate(User $user, Table $table, Event $event, string $subject): void
    {
        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/website/edit_table_participate.html.twig')
            ->context([
                'subject'   => $subject,
                'table'     => $table,
                'user'      => $user,
                'event'     => $event,
                'signedUrl' => $this->generateUrl('event.show', ['slug' => $event->getSlug()], 0),
            ]);
        $this->mailer->send($email);
    }
}

