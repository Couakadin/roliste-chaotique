<?php

namespace App\Repository\System;

use App\Entity\System\System;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<System>
 *
 * @method System|null find($id, $lockMode = null, $lockVersion = null)
 * @method System|null findOneBy(array $criteria, array $orderBy = null)
 * @method System[]    findAll()
 * @method System[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SystemRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, System::class);
    }

    /**
     * @param System $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(System $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param System $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(System $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
