<?php

namespace App\Repository\Folder;

use App\Entity\Folder\Folder;
use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Folder>
 *
 * @method Folder|null find($id, $lockMode = null, $lockVersion = null)
 * @method Folder|null findOneBy(array $criteria, array $orderBy = null)
 * @method Folder[]    findAll()
 * @method Folder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FolderRepository extends NestedTreeRepository
{
    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct($manager, $manager->getClassMetadata(Folder::class));
    }

    public function save(Folder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Folder $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTreeQuery(UserInterface|User $owner): string|array|int|float
    {
        return $this->createQueryBuilder('f')
            ->select('s.originalName, f.lvl, f.slug, f.title, f.lft, f.rgt')
            ->leftJoin('f.storages', 's')
            ->where('f.owner = :user')
            ->setParameter('user', $owner)
            ->orderBy('f.root, f.lft', 'ASC')
            ->getQuery()
            ->getArrayResult();
    }
}
