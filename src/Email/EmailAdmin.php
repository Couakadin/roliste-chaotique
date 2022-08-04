<?php

namespace App\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailAdmin extends AbstractController
{
    private const ADMIN_NAME = 'Rôliste chaotique';
    private const ADMIN_EMAIL = 'contact@roliste-chaotique.be';

    /**
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(private readonly MailerInterface $mailer, private readonly TranslatorInterface $translator) { }

    /**
     * @param $user
     *
     * @throws TransportExceptionInterface
     */
    public function emailNewInscriptionAdmin($user)
    {
        $subject = $this->translator->trans('admin.new_inscription.subject');

        $email = (new TemplatedEmail())
            ->from(new Address(self::ADMIN_EMAIL, self::ADMIN_NAME))
            ->to(self::ADMIN_EMAIL)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_new_inscription.html.twig')
            ->context([
                'subject'   => $subject,
                'userEmail' => $user->getEmail(),
                'signedUrl' => $this->generateUrl('account.index', ['slug' => $user->getSlug()], 0),
                'createdAt' => $user->getCreatedAt()->format('H:i:s Y-m-d')
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param $emailContact
     * @param $subject
     * @param $content
     *
     * @throws TransportExceptionInterface
     */
    public function newContactAdmin($emailContact, $subject, $content)
    {
        $email = (new TemplatedEmail())
            ->from($emailContact)
            ->to(self::ADMIN_EMAIL)
            ->replyTo($emailContact)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_new_contact.html.twig')
            ->context([
                'subject'      => $subject,
                'emailContact' => $emailContact,
                'content'      => $content
            ]);
        $this->mailer->send($email);
    }
}
