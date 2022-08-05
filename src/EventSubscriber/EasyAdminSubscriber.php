<?php

namespace App\EventSubscriber;

use App\Email\Email;
use App\Entity\Avatar\Avatar;
use App\Entity\Table\Table;
use App\Entity\Table\TableInscription;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly UserPasswordHasherInterface $hash,
        private readonly ParameterBagInterface       $parameterBag,
        private readonly Email                       $email
    )
    {
    }

    #[ArrayShape([
        BeforeEntityPersistedEvent::class => "string[]",
        AfterEntityDeletedEvent::class    => "string[]",
        BeforeEntityDeletedEvent::class   => "string[]",
        AfterEntityUpdatedEvent::class    => "string[]"
    ])]
    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => ['addUser'],
            AfterEntityDeletedEvent::class    => ['deleteImage'],
            BeforeEntityDeletedEvent::class   => ['removeRelation'],
            AfterEntityUpdatedEvent::class    => ['emailTableInscription']
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
        if ($entity instanceof Table) {
            $imgPath = $this->parameterBag->get('kernel.project_dir') .
                '/public/uploads/images/tables/' . $entity->getPicture();
        }
        if (file_exists($imgPath)) unlink($imgPath);
    }

    public function removeRelation(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $user = $this->entityManager->getRepository(User::class);

        if ($entity instanceof Avatar) {
            $users = $user->findBy(['avatar' => $entity]);

            foreach ($users as $user) {
                $entity->removeUser($user);
            }
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function emailTableInscription(AfterEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (!($entity instanceof TableInscription)) {
            return;
        }

        if (TableInscription::STATUS['accepted'] === $entity->getStatus()) {
            if ($entity->isEmailSending()) {
                $this->email->tableInscription('accepted', $entity->getUser(), $entity->getTable());
            }

            $tableRepo = $this->entityManager->getRepository(Table::class);
            $table = $tableRepo->find($entity->getTable());
            $table->addMember($entity->getUser());

            $this->entityManager->flush();
        } elseif (TableInscription::STATUS['declined'] === $entity->getStatus()) {
            if ($entity->isEmailSending()) {
                $this->email->tableInscription('declined', $entity->getUser(), $entity->getTable());
            }

            $tableRepo = $this->entityManager->getRepository(Table::class);
            $table = $tableRepo->find($entity->getTable());
            $table->removeMember($entity->getUser());

            $this->entityManager->flush();
        }
    }
}