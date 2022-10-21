<?php

namespace App\Repository\Event;

use App\Entity\Event\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
