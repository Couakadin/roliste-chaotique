<?php

namespace App\DataFixtures\Event;

use App\DataFixtures\Table\TableFixtures;
use App\Entity\Event\Event;
use App\Entity\Table\Table;
use App\Entity\Zone\Zone;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class EventFixtures extends Fixture implements OrderedFixtureInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager) { }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 5; $i++) {
            $entity = new Event();
            $entity->setName('Symbaroum-' . $i);
            $entity->setType(Event::TYPE['campaign']);
            $entity->setTable($this->findTable());
            $entity->setStart(new DateTimeImmutable());
            $entity->setEnd(new DateTimeImmutable());
            $entity->setZone($this->findZone());
            // No members. I prefer to set them manually in order to make tests.

            $manager->persist($entity);
        }

        $manager->flush();
    }

    private function findTable()
    {
        return $this->entityManager
            ->getRepository(Table::class)
            ->findOneBy(['name' => TableFixtures::TABLES[1]]);
    }

    private function findZone()
    {
        return $this->entityManager
            ->getRepository(Zone::class)
            ->findOneBy(['locality' => 'Boussu']);
    }

    public function getOrder(): int
    {
        return 3;
    }
}
