<?php

namespace App\DataFixtures\User;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ADMIN_USERNAME = 'MJ Sadique';

    private UserPasswordHasherInterface $passwordHash;

    public function __construct(UserPasswordHasherInterface $passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i < 20; $i++) {
            $user = new User();
            $user->setEmail('user.email.' . $i . '@fixtures.wip');
            $user->setUsername('user' . $i);

            $password = $this->passwordHash->hashPassword($user, 'password');
            $user->setPassword($password);

            $manager->persist($user);
        }

        $admin = new User();
        $admin->setEmail("contact@roliste-chaotique.be");
        $admin->setUsername(self::ADMIN_USERNAME);
        $admin->setRoles(['ROLE_ADMIN']);

        $password = $this->passwordHash->hashPassword($admin, 'password');
        $admin->setPassword($password);

        $manager->persist($admin);
        $manager->flush();
    }

    public function getOrder(): int
    {
        return 1;
    }
}
