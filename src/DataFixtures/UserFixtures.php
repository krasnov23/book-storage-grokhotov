<?php

namespace App\DataFixtures;

use App\Entity\UserEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new UserEntity();
        $user1->setEmail('test@test.com');
        $user1->setPassword($this->hasher->hashPassword($user1,12345678));
        $manager->persist($user1);

        $manager->flush();
    }
}
