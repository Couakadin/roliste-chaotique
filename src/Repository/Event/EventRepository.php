<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use App\Entity\User\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
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
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @param Event $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Event $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(Event $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string|null $search
     *
     * @return QueryBuilder
     */
    public function paginateEvents(?string $search): QueryBuilder
    {
        $query = $this->createQueryBuilder('e');
        if ($search) {
            $query
                ->leftJoin('e.table', 't')
                ->leftJoin('e.zone', 'z')
                ->leftJoin('e.master', 'm')
                ->where(
                    'e.name LIKE :search OR 
                t.name LIKE :search OR
                z.locality LIKE :search OR
                m.username LIKE :search
                ')
                ->setParameter(':search', '%' . $search . '%');
        }
        $query
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $query;
    }

    /**
     * @return float|int|mixed|string
     */
    public function findLastEvents(): mixed
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
     * @param User|UserInterface $user
     *
     * @return float|int|mixed|string
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findTotalEventsByParticipate(User|UserInterface $user): mixed
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
     * @param User|UserInterface $user
     *
     * @return float|int|mixed|string
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findTotalEventsByMaster(User|UserInterface $user): mixed
    {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.master', 'p')
            ->where('p.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return float|int|mixed|string
     */
    public function findEventsWeekBefore(): mixed
    {
        $week = new DateTimeImmutable('+1 week');

        return $this->createQueryBuilder('e')
            ->leftJoin('e.participate', 'p')
            ->where('e.start LIKE :week')
            ->setParameter('week', $week->format('Y-m-d') . '%')
            ->getQuery()
            ->getResult();
    }
}
