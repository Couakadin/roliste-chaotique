<?php

namespace App\RC\BadgeBundle\src\Manager;

use App\Entity\User\User;
use App\RC\BadgeBundle\src\Entity\Badge;
use App\RC\BadgeBundle\src\Entity\BadgeUnlock;
use App\RC\BadgeBundle\src\Event\BadgeUnlockedEvent;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BadgeManager
{
    public function __construct(
        public readonly ObjectManager            $manager,
        public readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    /**
     * Check if a badge exists for this action and action occurrence +
     * unlock it for the current User
     *
     * @param User $user
     * @param string $action
     * @param int $action_count
     *
     * @return void
     */
    public function checkAndUnlock(User $user, string $action, int $action_count): void
    {
        try {
            $badge = $this->manager->getRepository(Badge::class)
                ->findWithUnlockForAction($user->getId(), $action, $action_count);

            if ($badge->getUnlocks()->isEmpty()) {
                $unlock = (new BadgeUnlock())
                    ->setBadge($badge)
                    ->setUser($user);

                $this->manager->persist($unlock);
                $this->manager->flush();

                // Set an event to inform the app a Badge is unlocked
                $this->dispatcher->dispatch(new BadgeUnlockedEvent($unlock), BadgeUnlockedEvent::NAME);
            }
        } catch (NoResultException) {
        }
    }

    /**
     * Get Badges unlocked for the current User
     *
     * @param User $user
     * @return array
     */
    public function getBadgeFor(User $user): array
    {
        return $this->manager->getRepository(Badge::class)->findUnlockedFor($user->getId());
    }
}