<?php

namespace App\Repository\Table;

use App\Entity\Table\Table;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Table>
 *
 * @method Table|null find($id, $lockMode = null, $lockVersion = null)
 * @method Table|null findOneBy(array $criteria, array $orderBy = null)
 * @method Table[]    findAll()
 * @method Table[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TableRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Table::class);
    }

    /**
     * @param Table $entity
     * @param bool $flush
     *
     * @return void
     */
    public function add(Table $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param Table $entity
     * @param bool $flush
     *
     * @return void
     */
    public function remove(Table $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return float|int|mixed|string
     */
    public function findShowcase(): mixed
    {
        return $this->createQueryBuilder('g')
            ->where('g.showcase = :showcase')
            ->setParameter('showcase', 1)
            ->orderBy('g.createdAt')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $table
     *
     * @return float|int|mixed|string
     */
    public function nextEvents(int $table): mixed
    {
        return $this->createQueryBuilder('t')
            ->select('e.name, e.slug')
            ->leftJoin('t.events', 'e')
            ->where('e.start > :date AND :date < e.end')
            ->andWhere('t.id = :table')
            ->setParameter('date', new DateTimeImmutable('now'))
            ->setParameter('table', $table)
            ->orderBy('e.createdAt')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
}
