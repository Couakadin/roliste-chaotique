<?php

namespace App\EventSubscriber;

use App\Entity\Avatar\Avatar;
use App\Entity\Game\Game;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private EntityManagerInterface      $entityManager;
    private UserPasswordHasherInterface $hash;
    private ParameterBagInterface       $parameterBag;

    public function __construct(
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $hash,
        ParameterBagInterface       $parameterBag)
    {
        $this->entityManager = $entityManager;
        $this->hash = $hash;
        $this->parameterBag = $parameterBag;
    }

    #[ArrayShape([BeforeEntityPersistedEvent::class => "string[]", AfterEntityDeletedEvent::class => "string[]", BeforeEntityDeletedEvent::class => "string[]"])]
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
            AfterEntityDeletedEvent::class    => ['deleteImage'],
            BeforeEntityDeletedEvent::class   => ['removeRelation']
        ];
    }

    public function addUser(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }
        $this->setPassword($entity);
    }

    /**
     * @param User $entity
     */
    public function setPassword(User $entity): void
    {
        $pass = $entity->getPassword();

        $entity->setPassword(
            $this->hash->hashPassword(
                $entity,
                $pass
            )
        );
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function deleteImage(AfterEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $imgPath = '';

        if ($entity instanceof Avatar) {
            $imgPath = $this->parameterBag->get('kernel.project_dir') .
                '/public/uploads/images/avatars/' . $entity->getPath();
        }
        if ($entity instanceof Game) {
            $imgPath = $this->parameterBag->get('kernel.project_dir') .
                '/public/uploads/images/games/' . $entity->getPicture();
        }
        if (file_exists($imgPath)) unlink($imgPath);
    }

    public function removeRelation(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $user = $this->entityManager->getRepository('App:User\User');

        if ($entity instanceof Avatar) {
            $users = $user->findBy(['avatar' => $entity]);

            foreach ($users as $user) {
                $entity->removeUser($user);
            }
        }
    }
}