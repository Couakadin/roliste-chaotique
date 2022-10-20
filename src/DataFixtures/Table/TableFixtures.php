<?php

namespace App\DataFixtures\Table;

use App\Entity\Table\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TableFixtures extends Fixture implements OrderedFixtureInterface
{
    public const TABLES = [
        'Symbaroum',
        'Coriolis, le Troisième Horizon',
        'Les Oubliés',
        'Alien',
        'Tales from the Loop',
        'Things from the Flood',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TABLES as $table) {
            $entity = new Table();
            $entity->setName($table);
            $entity->setPicture(strtolower(str_replace([' ', ':', ',', 'œ', '--', '\''], ['-', '', '', 'oe', '-', '-'], $table)) . '.webp');
            $entity->setShowcase(true);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 2;
    }
}
