<?php

namespace App\Repository\Badge;

use App\Entity\Badge\BadgeUnlock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BadgeUnlock>
 *
 * @method BadgeUnlock|null find($id, $lockMode = null, $lockVersion = null)
 * @method BadgeUnlock|null findOneBy(array $criteria, array $orderBy = null)
 * @method BadgeUnlock[]    findAll()
 * @method BadgeUnlock[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeUnlockRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BadgeUnlock::class);
    }

    /**
     * @param BadgeUnlock $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(BadgeUnlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param BadgeUnlock $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(BadgeUnlock $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
