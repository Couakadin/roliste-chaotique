<?php

namespace App\Repository\Event;

use App\Entity\Event\EventColor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventColor>
 *
 * @method EventColor|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventColor|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventColor[]    findAll()
 * @method EventColor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventColorRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventColor::class);
    }

    /**
     * @param EventColor $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(EventColor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param EventColor $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(EventColor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
