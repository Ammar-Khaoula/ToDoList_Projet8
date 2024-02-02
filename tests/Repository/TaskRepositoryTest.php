<?php

namespace App\tests\Repository;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{

    public function testCount()
    {

        self::bootKernel();
        $container = static::getContainer();

        $task = $container->get(TaskRepository::class)->count([]);
        $this->assertEquals(12, $task);
    }
}
