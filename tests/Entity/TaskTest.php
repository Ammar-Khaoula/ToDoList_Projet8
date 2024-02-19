<?php

namespace App\test\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function getTaskEntity(): Task
    {
        $user = (new User())
        ->setUsername('User1')
        ->setEmail('leroy.marcelle@sfr.fr')
        ->setPassword('password')
        ->setRoles(['ROLE_USER']);

        return(new Task())
            ->setTitle('task_modifier')
            ->setContent('contenu de la tache huit')
            ->setCreatedAt(new \DateTimeImmutable)
            ->setUser($user);

    }

    public function testTaskEntity(): void
    {
        $task = $this->getTaskEntity();
            
        $this->assertEquals($task->getId(), null);
        $this->assertEquals('task_modifier', $task->getTitle());
        $this->assertEquals('contenu de la tache huit', $task->getContent());
        $this->assertEquals(false, $task->isDone());
        $this->assertEquals(true, $task->getUser() instanceof User);
        $this->assertEquals(true, $task->getCreatedAt() instanceof DateTimeImmutable);
    }

    public function testValidateTask(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $task = $this->getTaskEntity();
        $errors = $container->get('validator')->validate($task);
        $this->assertCount(0, $errors);
    }
    public function testInvalidateTask(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $task = $this->getTaskEntity();
        $task->setTitle('');
        $errors = $container->get('validator')->validate($task);
        $this->assertCount(1, $errors);
    }

    public function testvalidBlanktitle(): void
    {
        self::bootKernel();
        $container = static::getContainer();

        $task = $this->getTaskEntity();
       // $container($task->setContent(''), 1);

        $task->setTitle('tache_1');

        $errors = $container->get('validator')->validate($task);
        $this->assertCount(0, $errors);
    }
}
