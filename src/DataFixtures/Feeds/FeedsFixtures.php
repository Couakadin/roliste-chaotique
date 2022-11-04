<?php

namespace App\DataFixtures\Feeds;

use App\Entity\Feeds\Feeds;
use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class FeedsFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $author = $this->entityManager->getRepository(User::class)
            ->findOneBy(['username' => 'MJ Sadique']);

        for ($i = 1; $i < 50; $i++) {
            $entity = new Feeds();
            $entity->setContent($i . '---
            Lorem ipsum dolor sit amet. Sed assumenda explicabo qui placeat consequatur eum omnis rerum sit explicabo quia 33 nihil tenetur id omnis repudiandae. Quo velit soluta ut perspiciatis laudantium est mollitia galisum ut recusandae rerum!
            Quo illum deleniti ut distinctio sapiente ut eius nostrum? Ut expedita esse qui molestias soluta id magnam minima non laboriosam eius.
            Quo temporibus unde aut voluptate dolor aut commodi sequi et neque consequatur et odio voluptatibus qui tempora itaque. Et dicta dolorem cum tenetur sapiente vel molestiae neque. Ea incidunt temporibus 33 aperiam corporis quo earum natus eos blanditiis accusamus et fugiat voluptas. Ut odit mollitia 33 quasi quibusdam aut totam corporis sit consequatur fugit non nihil voluptates.');
            $entity->setAuthor($author);

            $manager->persist($entity);
        }

        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 4;
    }
}
