<?php

namespace App\Repository\Newsfeed;

use App\Entity\Newsfeed\Newsfeed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Newsfeed>
 *
 * @method Newsfeed|null find($id, $lockMode = null, $lockVersion = null)
 * @method Newsfeed|null findOneBy(array $criteria, array $orderBy = null)
 * @method Newsfeed[]    findAll()
 * @method Newsfeed[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsfeedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Newsfeed::class);
    }

    public function save(Newsfeed $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Newsfeed $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
