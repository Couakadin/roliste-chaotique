<?php

namespace App\Email;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

class Email extends AbstractController
{
    const ADMIN = 'Rôliste chaotique';

    private MailerInterface $mailer;

    private TranslatorInterface $translator;

    /**
     * Return admin email
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
     * @param $newToken
     * @param string $subject
     *
     * @throws TransportExceptionInterface
     */
    public function emailVerify($user, $newToken, string $subject)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->admin, self::ADMIN))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/verify_email.html.twig')
            ->context([
                'subject'   => $subject,
                'signedUrl' => $this->generateUrl('security.registration.verify_user_email', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param $user
     * @param $newToken
     * @param string $subject
     * @throws TransportExceptionInterface
     */
    public function forgottenPassword($user, $newToken, string $subject)
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->admin, self::ADMIN))
            ->to($user->getEmail())
            ->subject(ucfirst($subject))
            ->htmlTemplate('@email/forgotten_password.html.twig')
            ->context([
                'subject' => $subject,
                'signedUrl' => $this->generateUrl('front.home.forgotten_password.new', ['token' => $newToken->getToken()], 0),
                'expiredAt' => $newToken->getExpiredAt()
            ]);
        $this->mailer->send($email);
    }

    /**
     * @param $schedule
     *
     * @throws TransportExceptionInterface
     */
    /*
    public function emailNewSchedule($schedule)
    {
        $subject = 'Un programme a été ajouté sur MJ Sadique !';

        $userRepository = $this->getDoctrine()->getRepository('App:User\User');
        $users          = $userRepository->findByNotificationSchedule($schedule->getGuild());

        foreach ($users as $user) {
            $email = (new TemplatedEmail())
                ->from(new Address($this->admin, 'MJ Sadique'))
                ->to($user->getEmail())
                ->subject($subject)
                ->htmlTemplate('@email/new_schedule.html.twig')
                ->context([
                    'subject'  => $subject,
                    'schedule' => $schedule,
                    'user'     => $user,
                    'url'      => $this->generateUrl('front.schedule.show', ['id' => $schedule->getId()], 0),

                ]);
            $this->mailer->send($email);
        }
    }
    */
}

