<?php

namespace App\Service;

use App\Entity\Event\Event;
use App\Entity\Notification\Notification;
use App\Entity\User\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationManager
{
    public function __construct(
        public readonly ObjectManager $manager
    )
    {
    }

    public function notificationsByUser(User|UserInterface $user)
    {
        $notifications = $this->manager->getRepository(Notification::class)
            ->findBy(['user' => $user], ['createdAt' => 'DESC'], 15);

        foreach ($notifications as $notification) {
            if (in_array($notification->getType(), ['event-create', 'event-update']) ) {
                $event = $this->manager->getRepository(Event::class)
                    ->find($notification->getEntityId());

                $notification->event = $event;
            }
        }

        return $notifications;
    }
}