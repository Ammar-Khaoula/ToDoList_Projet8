<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Tests\Traits\CustomAssert;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserTest extends KernelTestCase
{

    /**
     * Get a valid Task entity
     *
     * @return \App\Entity\Task
     */
    public function getTaskEntity(): Task
    {
        return (new Task)
            ->setTitle('Task title')
            ->setContent('Task content')
            ->setCreatedAt(new \DateTimeImmutable());
    }

    public function getUserEntity(): User
    {
        $task = $this->getTaskEntity();
        return (new User())
            ->setEmail('User2@email.com')
            ->setUsername('User2')
            ->setPassword('password')
            ->setRoles(['ROLE_ADMIN'])
            ->addTask($task);
    }

    public function testUserEntity()
    {
        $task = $this->getTaskEntity();
        $user = (new User())
            ->setUsername('userTest')
            ->setEmail('userTest@gmail.fr')
            ->setPassword('password')
            ->setRoles(['ROLE_USER'])
            ->addTask($task);


        $this->assertEquals($user->getId(), null);
        $this->assertEquals('userTest', $user->getUsername());
        $this->assertEquals('userTest@gmail.fr', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }


    public function testValidRemoveTask()
    {
        $user = $this->getUserEntity();
        $task = $this->getTaskEntity();

        $this->assertNotEmpty($user->getTasks());
        $user->removeTask($task);
    }

    public function testInvalidPasswordUser()
    {
        $user = (new User());
        $user->setPassword('password');
        $this->assertNotEquals('12345', $user->getPassword());
    }
}
