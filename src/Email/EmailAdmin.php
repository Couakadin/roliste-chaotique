<?php

namespace App\Email;

use App\Entity\User\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailAdmin extends AbstractController
{
    private const ADMIN_EMAIL = 'webdev.valentin@gmail.com';

    /**
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator) { }

    /**
     * @param User|UserInterface $user
     *
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    public function inscriptionAdmin(User|UserInterface $user): void
    {
        $subject = $this->translator->trans('admin.inscription.subject');

        $email = (new TemplatedEmail())
            ->from(new Address(Email::ADMIN_EMAIL, Email::ADMIN_NAME))
            ->to(self::ADMIN_EMAIL)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_inscription.html.twig')
            ->context([
                'subject'   => $subject,
                'userEmail' => $user->getEmail(),
                'signedUrl' => $this->generateUrl('account.index', ['slug' => $user->getSlug()], 0),
                'createdAt' => $user->getCreatedAt()->format('H:i:s Y-m-d')
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param string $emailContact
     * @param string $subject
     * @param string $content
     *
     * @return void
     *
     * @throws TransportExceptionInterface
     */
    public function contactAdmin(string $emailContact, string $subject, string $content): void
    {
        $email = (new TemplatedEmail())
            ->from($emailContact)
            ->to(self::ADMIN_EMAIL)
            ->replyTo($emailContact)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_contact.html.twig')
            ->context([
                'subject'      => $subject,
                'emailContact' => $emailContact,
                'content'      => $content
            ]);
        $this->mailer->send($email);
    }
}
