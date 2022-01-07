<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $dummyData = Yaml::parseFile(__DIR__ . '/Data/user.yaml');

        foreach ($dummyData['users'] as $dummyUser) {
            $user = new User();
            $user
                ->setUsername($dummyUser['username'])
                ->setEmail($dummyUser['email'])
                ->setPassword($this->passwordHasher->hashPassword($user, $dummyUser['password']))
                ->setRoles($dummyUser['roles']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
