<?php

namespace App\RC\BadgeBundle\src\Event;


use App\Entity\User\User;
use App\RC\BadgeBundle\src\Entity\Badge;
use App\RC\BadgeBundle\src\Entity\BadgeUnlock;
use Symfony\Contracts\EventDispatcher\Event;

class BadgeUnlockedEvent extends Event
{
    const NAME = 'badge.unlock';

    public function __construct(private readonly BadgeUnlock $badgeUnlock) {}

    public function getBadgeUnlock(): BadgeUnlock
    {
        return $this->badgeUnlock;
    }

    public function getBadge(): Badge
    {
        return $this->badgeUnlock->getBadge();
    }

    public function getUser(): User
    {
        return $this->badgeUnlock->getUser();
    }
}