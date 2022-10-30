<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function add(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function search(?string $search)
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.table', 't')
            ->leftJoin('e.zone', 'z')
            ->leftJoin('e.master', 'm')
            ->where(
                'e.name LIKE :search OR 
                t.name LIKE :search OR
                z.locality LIKE :search OR
                m.username LIKE :search
                ')
            ->orderBy('e.createdAt', 'DESC')
            ->setParameter(':search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
    }

    public function findLastEvents()
    {
        return $this->createQueryBuilder('e')
            ->leftJoin('e.master', 'm')
            ->leftJoin('e.zone', 'z')
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findTotalEventsByParticipate(User|UserInterface $user)
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.participate', 'p')
            ->where('p.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findTotalEventsByMaster(User|UserInterface $user)
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.master', 'p')
            ->where('p.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
