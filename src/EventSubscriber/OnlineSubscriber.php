<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class OnlineSubscriber implements EventSubscriberInterface
{
    public function __construct(
        public readonly Security               $security,
        public readonly EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    public function onKernelRequest()
    {
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $user = ($this->entityManager->getRepository(User::class))
            ->find($user);

        if ($user->getLoggedAt() < new DateTime('now')) {
            $user->setLoggedAt(new DateTime('+5 minutes'));
            $this->entityManager->flush();
        }
    }
}