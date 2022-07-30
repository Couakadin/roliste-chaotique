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
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;
    /**
     * @var string
     */
    private string $admin;

    /**
     * EmailController constructor.
     *
     * @param MailerInterface $mailer
     * @param TranslatorInterface $translator
     */
    public function __construct(
        MailerInterface     $mailer,
        TranslatorInterface $translator
    )
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->admin = 'contact@roliste-chaotique.be';
    }

    /**
     * @param $user
     *
     * @throws TransportExceptionInterface
     */
    public function emailNewInscriptionAdmin($user)
    {
        $subject = $this->translator->trans('admin.new_inscription.subject');

        $email = (new TemplatedEmail())
            ->from(new Address($this->admin, 'Rôliste chaotique'))
            ->to($this->admin)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_new_inscription.html.twig')
            ->context([
                'subject'   => $subject,
                'userEmail' => $user->getEmail(),
                'signedUrl' => $this->generateUrl('front.account.index', ['slug' => $user->getSlug()], 0),
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
            ->to($this->admin)
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

    /**
     * @throws TransportExceptionInterface
     */
    public function guildJoinRequest($username, $emailContact, $subject, $content)
    {
        $email = (new TemplatedEmail())
            ->from($emailContact)
            ->to($this->admin)
            ->replyTo($emailContact)
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/admin/admin_guild_join_request.html.twig')
            ->context([
                'subject'  => $subject,
                'username' => $username,
                'content'  => implode(', ', $content)
            ]);
        $this->mailer->send($email);
    }
}
