<?php

namespace App\DataFixtures\Guild;

use App\DataFixtures\User\UserFixtures;
use App\Entity\Guild\Guild;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class GuildFixtures extends Fixture implements OrderedFixtureInterface
{
    public const GUILDS = [
        'La Compagnie de la Strige',
        'Les Oubliés',
    ];

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::GUILDS as $guild) {
            $entity = new Guild();
            $entity->setName($guild);
            $entity->setMaster($this->findAdmin());

            foreach ($this->findMembers() as $member) {
                $member->addGuildMember($entity);
            }

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function findAdmin()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => UserFixtures::ADMIN_USERNAME]);
    }

    private function findMembers()
    {
        return $this->entityManager
            ->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.email LIKE :email')
            ->setParameter('email', '%@fixtures.wip%')
            ->getQuery()
            ->getResult();
    }

    public function getOrder(): int
    {
        return 3;
    }
}
