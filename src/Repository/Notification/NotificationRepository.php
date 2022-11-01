<?php

namespace App\Repository\Notification;

use App\Entity\Notification\Notification;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Notification>
 *
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * @param Notification $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Notification $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(Notification $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array|User|UserInterface $user
     *
     * @return float|int|mixed|string
     */
    public function findAllByUser(array|User|UserInterface $user): mixed
    {
        return $this->createQueryBuilder('n')
            ->leftJoin('n.user', 'u')
            ->leftJoin('n.event', 'e')
            ->leftJoin('n.badge', 'b')
            ->where('u.id = :user')
            ->setParameter('user', $user)
            ->setMaxResults(15)
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param array|User|UserInterface $user
     *
     * @return float|int|mixed|string
     */
    public function findReadByUser(array|User|UserInterface $user): mixed
    {
        return $this->createQueryBuilder('n')
            ->where('n.isRead = false')
            ->andWhere('n.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
