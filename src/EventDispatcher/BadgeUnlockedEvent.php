<?php

namespace App\EventDispatcher;


use App\Entity\Badge\Badge;
use App\Entity\Badge\BadgeUnlock;
use App\Entity\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class BadgeUnlockedEvent extends Event
{
    public const NAME = 'badge.unlock';

    /**
     * @param BadgeUnlock $badgeUnlock
     */
    public function __construct(private readonly BadgeUnlock $badgeUnlock) {}

    /**
     * @return BadgeUnlock
     */
    public function getBadgeUnlock(): BadgeUnlock
    {
        return $this->badgeUnlock;
    }

    /**
     * @return Badge
     */
    public function getBadge(): Badge
    {
        return $this->badgeUnlock->getBadge();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->badgeUnlock->getUser();
    }
}
