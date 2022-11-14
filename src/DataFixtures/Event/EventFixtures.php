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
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager) { }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
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
            $entity->setInitiation(false);
            // No members. I prefer to set them manually in order to make tests.

            $manager->persist($entity);
        }

        $manager->flush();
    }

    /**
     * @return Table|mixed|object|null
     */
    private function findTable(): mixed
    {
        return $this->entityManager
            ->getRepository(Table::class)
            ->findOneBy(['name' => TableFixtures::TABLES[1]]);
    }

    /**
     * @return Zone|mixed|object|null
     */
    private function findZone(): mixed
    {
        return $this->entityManager
            ->getRepository(Zone::class)
            ->findOneBy(['locality' => 'Boussu']);
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 3;
    }
}
