<?php

namespace App\EventSubscriber;

use App\Entity\Notification\Notification;
use App\EventDispatcher\BadgeUnlockedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BadgeSubscriber implements EventSubscriberInterface
{
    /**
     * @param RequestStack $requestStack
     * @param EntityManagerInterface $entityManager
     */
    public function __construct
    (
        private readonly RequestStack           $requestStack,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    /**
     * @return string[]
     */
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
     *
     * @return void
     */
    public function onBadgeUnlock(BadgeUnlockedEvent $event): void
    {
        $notification = (new Notification())
            ->setUser($event->getUser())
            ->setBadge($event->getBadge())
            ->setType('badge-unlock')
            ->setIsRead(false);
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        $this->requestStack
            ->getCurrentRequest()
            ->getSession()
            ->getFlashBag()->add('badge', $event->getBadge()->getDescription());
    }
}