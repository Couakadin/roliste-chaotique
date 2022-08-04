<?php

namespace App\DataFixtures\Table;

use App\Entity\Table\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TableFixtures extends Fixture implements OrderedFixtureInterface
{
    public const TABLES = [
        'Donjons et Dragons',
        'Symbaroum',
        'Coriolis, le Troisième Horizon',
        'Cthulhu',
        'Les Oubliés',
        'Alien',
        'Cyberpunk RED',
        'Pathfinder',
        'Naheulbeuk',
        'Shadowrun',
        'Pavillon noir',
        'Vampire : La Mascarade',
        'Chroniques Oubliées',
        'L\'œil noir',
        'Tales from the Loop'
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
