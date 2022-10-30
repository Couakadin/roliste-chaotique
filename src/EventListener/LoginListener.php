<?php

namespace App\EventListener;

use App\Entity\Notification\Notification;
use App\Entity\User\User;
use App\Service\BadgeManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly BadgeManager           $badgeManager
    )
    {
    }

    /**
     * @throws Exception
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        // Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();

        // Update your field here.
        $user->setLoggedAt(new DateTimeImmutable());

        $now = new DateTimeImmutable('now');
        if ('10-17' <= $now->format('m-d') && $now->format('m-d') <= '10-31') {
            $this->badgeManager->checkAndUnlock($user, 'halloween', 1);
        }
        if ('12-17' <= $now->format('m-d') && $now->format('m-d') <= '12-31') {
            $this->badgeManager->checkAndUnlock($user, 'christmas', 1);
        }

        if ($this->accountBirthday($user, '+1 year')->format('Y-m-d') <= $now->format('Y-m-d')) {
            $this->badgeManager->checkAndUnlock($user, 'account-birthday', 1);
        }
        if ($this->accountBirthday($user, '+2 years')->format('Y-m-d') <= $now->format('Y-m-d')) {
            $this->badgeManager->checkAndUnlock($user, 'account-birthday', 2);
        }
        if ($this->accountBirthday($user, '+3 years')->format('Y-m-d') <= $now->format('Y-m-d')) {
            $this->badgeManager->checkAndUnlock($user, 'account-birthday', 3);
        }


        $notifications = $this->entityManager->getRepository(Notification::class)
            ->findBy(['user' => $user]);

        if ($notifications) {
            $date = new DateTimeImmutable('-3 months');
            foreach ($notifications as $notification) {
                if ($notification->getCreatedAt()->format('Y-m-d') < $date->format('Y-m-d')) {
                    $this->entityManager->remove($notification);
                }
            }
        }

        // Persist the data to database.
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    /**
     * @throws Exception
     */
    private function accountBirthday(User|UserInterface $user, string $year): DateTimeImmutable
    {
        return new DateTimeImmutable($user->getCreatedAt()->modify($year)->format('Y-m-d'));
    }
}