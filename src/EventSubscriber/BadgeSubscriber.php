<?php

namespace App\EventSubscriber;

use App\EventDispatcher\BadgeUnlockedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BadgeSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly RequestStack $requestStack)
    {}

    public static function getSubscribedEvents(): array
    {
        return [
            BadgeUnlockedEvent::NAME => 'onBadgeUnlock',
        ];
    }

    /**
     * When a badge is unlocked we send an email
     *
     * @param BadgeUnlockedEvent $event
     * @return void
     */
    public function onBadgeUnlock(BadgeUnlockedEvent $event): void
    {
        $this->requestStack
            ->getCurrentRequest()
            ->getSession()
            ->getFlashBag()->add('badge', $event->getBadge()->getName());
    }
}