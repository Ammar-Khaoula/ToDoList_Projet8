<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    public const TASK_REFERENCE = 'task';
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function load(ObjectManager $manager): void
    {
        $tasks = [
            [
                'title' => 'task_1',
                'content' => 'contenu de la 1er tache',
            ],
            [
                'title' => 'task_2',
                'content' => 'contenu de la 2eme tache',
            ],
            [
                'title' => 'task_3',
                'content' => 'contenu de la tache troi',
            ],
            [
                'title' => 'task_4',
                'content' => 'contenu de la tache quatre',
            ],
            [
                'title' => 'task_5',
                'content' => 'contenu de la tache cinq',
            ],
            [
                'title' => 'task_6',
                'content' => 'contenu de la tache six',
            ],
            [
                'title' => 'task_7',
                'content' => 'contenu de la tache Sept',
            ],
            [
                'title' => 'task_8',
                'content' => 'contenu de la tache huit',
            ],
            [
                'title' => 'task_9',
                'content' => 'contenu de la tache neuf',
            ],
        ];

        //Create a anonymous task
        for ($i = 0; $i < 3; $i++) {
            $task = (new Task())
                ->setTitle("anonymous task")
                ->setContent("contenu de la tâche")
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUser(null);

            $manager->persist($task);
        }

        $faker = \Faker\Factory::create('fr_FR');
        $user = $this->userRepo->findAll();
        foreach ($tasks as $index => $userData) {

            $task = (new Task())
                ->setTitle($userData['title'])
                ->setContent($userData['content'])
                ->setCreatedAt(new \DateTimeImmutable())
                ->setUser($faker->randomElement($user));

            $manager->persist($task);

            $this->addReference(self::TASK_REFERENCE . $index, $task);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
