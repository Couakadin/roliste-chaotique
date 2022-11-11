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

    public function getTreeQuery(UserInterface|User $owner, ?Folder $parent = null): array|float|int|string
    {
        $query = $this->createQueryBuilder('f')
            ->where('f.owner = :owner');

        if (null !== $parent) {
            $query
                ->andWhere('f.parent = :parent')
                ->setParameter('parent', $parent);
        }

        $query
            ->setParameter('owner', $owner)
            ->orderBy('f.root, f.lft', 'ASC');

        return $query->getQuery()
            ->getArrayResult();
    }
}
