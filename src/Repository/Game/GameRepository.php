<?php

namespace App\Repository\Game;

use App\Entity\Game\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
 * @method Game[]    findAll()
 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findShowcase()
    {
        return $this->createQueryBuilder('g')
            ->where('g.showcase = :showcase')
            ->setParameter('showcase', 1)
            ->orderBy('g.createdAt')
            ->setMaxResults(15)
            ->getQuery()
            ->getResult();
    }
}
