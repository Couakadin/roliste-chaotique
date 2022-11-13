<?php

namespace App\Service;

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
        return $this->manager->getRepository(Notification::class)
            ->findAllByUser($user);
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