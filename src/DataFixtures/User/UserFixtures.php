<?php

namespace App\DataFixtures\User;

use App\Entity\User\User;
use App\Entity\User\UserParameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    private const ADMIN_USERNAME = 'MJ Sadique';
    private const ADMIN_EMAIL = 'contact@roliste-chaotique.be';

    /**
     * @param UserPasswordHasherInterface $passwordHash
     */
    public function __construct(private readonly UserPasswordHasherInterface $passwordHash) { }

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('email.' . $i . '@fixtures.wip');
            $user->setUsername('user' . $i);

            $password = $this->passwordHash->hashPassword($user, 'password');
            $user->setPassword($password);

            $userParameter = (new UserParameter())
                ->setUser($user);

            $manager->persist($user);
            $manager->persist($userParameter);
        }

        $admin = new User();
        $admin->setEmail(self::ADMIN_EMAIL);
        $admin->setUsername(self::ADMIN_USERNAME);
        $admin->setRoles(['ROLE_ADMIN']);

        $password = $this->passwordHash->hashPassword($admin, 'password');
        $admin->setPassword($password);

        $adminParameter = (new UserParameter())
            ->setUser($admin);

        $manager->persist($admin);
        $manager->persist($adminParameter);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return 1;
    }
}
