<?php

namespace App\EventListener;

use App\Entity\Notification\Notification;
use App\Service\BadgeManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    public function __construct
    (
        private readonly EntityManagerInterface $entityManager,
        private readonly BadgeManager $badgeManager
    )
    {
    }

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
}