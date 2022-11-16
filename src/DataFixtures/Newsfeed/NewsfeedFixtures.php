<?php

    namespace App\DataFixtures\Newsfeed;

    use App\Entity\Newsfeed\Newsfeed;
    use App\Entity\User\User;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
    use Doctrine\Persistence\ObjectManager;

    class NewsfeedFixtures extends Fixture implements OrderedFixtureInterface
    {
        public function load(ObjectManager $manager): void
        {
            $user = $manager->getRepository(User::class)
                            ->findOneBy(['email' => 'contact@roliste-chaotique.be']);

            for($i = 1 ; $i < 20 ; $i++) {
                $newsfeed = (new Newsfeed())
                    ->setContent('Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                    Ab, autem blanditiis consequatur deleniti dolores eius eos harum illo, 
                    inventore ipsam laudantium neque nihil, odit quidem sequi tempora totam velit vero.')
                    ->setAuthor($user);

                $manager->persist($newsfeed);
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
