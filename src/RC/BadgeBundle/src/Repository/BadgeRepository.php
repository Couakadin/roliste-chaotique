<?php

namespace App\RC\BadgeBundle\src\Repository;

use App\RC\BadgeBundle\src\Entity\Badge;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Badge>
 *
 * @method Badge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badge[]    findAll()
 * @method Badge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badge::class);
    }

    public function save(Badge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Badge $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param int $user_id
     * @param string $action
     * @param int $action_count
     *
     * @return Badge
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findWithUnlockForAction(int $user_id, string $action, int $action_count): Badge
    {
        return $this->createQueryBuilder('b')
            ->where('b.actionName = :action_name')
            ->andWhere('b.actionCount = :action_count')
            ->andWhere('u.user = :user_id OR u.user IS NULL')
            ->leftJoin('b.unlocks', 'u', Expr\Join::WITH, 'u.user = :user_id')
            ->select('b, u')
            ->setParameters([
                'action_count' => $action_count,
                'action_name'  => $action,
                'user_id'      => $user_id
            ])
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * Find all Badges unlocked by a specific User
     *
     * @param int $user_id
     * @return Badge[]
     */
    public function findUnlockedFor(int $user_id): array
    {
        return $this->createQueryBuilder('b')
            ->innerJoin('b.unlocks', 'u')
            ->where('u.user = :user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult();
    }
}
