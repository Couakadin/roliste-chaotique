<?php

namespace App\Service;

use App\Entity\Event\Event;
use App\Entity\Notification\Notification;
use App\Entity\User\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\User\UserInterface;

class NotificationManager
{
    /**
     * @param ObjectManager $manager
     */
    public function __construct(
        public readonly ObjectManager $manager
    )
    {
    }

    /**
     * @param User|UserInterface $user
     *
     * @return array
     */
    public function notificationsByUser(User|UserInterface $user): array
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

    /**
     * @param User $user
     *
     * @return float|int|mixed|string
     */
    public function findReadByUser(User $user)
    {
        return $this->manager->getRepository(Notification::class)
            ->findReadByUser(['user' => $user]);
    }
}