<?php

namespace App\EventDispatcher;


use App\Entity\Badge\Badge;
use App\Entity\Badge\BadgeUnlock;
use App\Entity\User\User;
use Symfony\Contracts\EventDispatcher\Event;

class BadgeUnlockedEvent extends Event
{
    public const NAME = 'badge.unlock';

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
