<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

/**
 * @codeCoverageIgnore
 */
class UserFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $user = new User();
        $user->setEmail('admin@email.com')
            ->setUsername('Admin')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->passwordHasher->hashPassword($user, 'password'));
        $manager->persist($user);

        for ($i = 0; $i < 9; $i++) {
            $user = new User();
            $user->setEmail($faker->freeEmail())
                ->setUsername('User' . $i + 1)
                ->setRoles(['ROLE_User'])
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}