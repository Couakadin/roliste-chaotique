<?php

namespace App\Repository\Feeds;

use App\Entity\Feeds\Feeds;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Feeds>
 *
 * @method Feeds|null find($id, $lockMode = null, $lockVersion = null)
 * @method Feeds|null findOneBy(array $criteria, array $orderBy = null)
 * @method Feeds[]    findAll()
 * @method Feeds[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FeedsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Feeds::class);
    }

    public function save(Feeds $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Feeds $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getAllFeeds(): Query
    {
        return $this->createQueryBuilder('f')
            ->getQuery();
    }
}
