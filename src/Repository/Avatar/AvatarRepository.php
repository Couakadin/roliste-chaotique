<?php

namespace App\Repository\Avatar;

use App\Entity\Avatar\Avatar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Avatar|null find($id, $lockMode = null, $lockVersion = null)
 * @method Avatar|null findOneBy(array $criteria, array $orderBy = null)
 * @method Avatar[]    findAll()
 * @method Avatar[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AvatarRepository extends ServiceEntityRepository
{
    const DEFAULT_AVATAR = 'female-tiefflin.png';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avatar::class);
    }

    /**
     * @return int|mixed|string|null
     * @throws NonUniqueResultException
     */
    public function findDefaultAvatar(): mixed
    {
        return $this->createQueryBuilder('a')
            ->where('a.path = :avatar')
            ->setParameter('avatar', self::DEFAULT_AVATAR)
            ->getQuery()
            ->getOneOrNullResult();
    }
}