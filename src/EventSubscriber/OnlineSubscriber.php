<?php

namespace App\EventSubscriber;

use App\Entity\User\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Security;

class OnlineSubscriber implements EventSubscriberInterface
{
    /**
     * @param Security $security
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        public readonly Security               $security,
        public readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => 'onKernelRequest',
        ];
    }

    /**
     * @return void
     */
    public function onKernelRequest(): void
    {
        $user = $this->security->getUser();

        if (!$user) {
            return;
        }

        $user = ($this->entityManager->getRepository(User::class))
            ->find($user);

        if ($user->getLoggedAt() < new DateTimeImmutable('now')) {
            $user->setLoggedAt(new DateTimeImmutable('+5 minutes'));
            $this->entityManager->flush();
        }
    }
}
