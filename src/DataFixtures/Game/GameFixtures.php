<?php

namespace App\DataFixtures\Game;

use App\Entity\Game\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture implements OrderedFixtureInterface
{
    public const GAMES = [
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
        foreach (self::GAMES as $game) {
            $entity = new Game();
            $entity->setName($game);
            $entity->setPicture(strtolower(str_replace([' ', ':', ',', 'œ', '--', '\''], ['-', '', '', 'oe', '-', '-'], $game)) . '.webp');
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
