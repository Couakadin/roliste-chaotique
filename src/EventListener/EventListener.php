<?php

namespace App\EventListener;

use App\Email\Email;
use App\Entity\Event\Event;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EventListener
{
    public function __construct(private readonly Email $email){}

    /**
     * @throws TransportExceptionInterface
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $object = $args->getObject();

        if (!$object instanceof Event) {
            return;
        }

        $users = $object->getTable()?->getFavorite();

        foreach ($users as $user) {
            $this->email->newEventForUserInFavorite($object, $user);
        }
    }
}
