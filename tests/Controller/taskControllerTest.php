<?php

namespace App\Tests\Controller;

use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListTasksNoAuth(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/tasks');

        $this->assertResponseRedirects();
        $client->followRedirect();
        //expected a redirection to /login
        $this->assertRouteSame('app_login');
    }

    public function testListTasks(): Void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('User9');
        //log a user
        $client
            ->loginUser($user)
            ->request(Request::METHOD_GET, '/tasks');
        //Expected a successful response
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('task_list');
    }

    public function testListTaskCompleted(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('User9');
        //log a user
        $client
            ->loginUser($user)
            ->request(Request::METHOD_GET, '/tasksCompleted');
        //Expect HTTP reponse 200
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('tasks_completed');
    }

    public function testListTaskToDo(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('User9');
        //log a user
        $client
            ->loginUser($user)
            ->request(Request::METHOD_GET, '/tasksToDo');
        //Expect HTTP reponse 200
        $this->assertResponseIsSuccessful();
        $this->assertRouteSame('tasks_to_do');
    }

    public function testTaskCreate(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $user = $userRepository->findOneByUsername('dali');
        //log a user
        $client
            ->loginUser($user)
            ->request('GET', '/tasks/create');
        //Create a task
        $client->submitForm(
            'Ajouter',
            [
                'task[title]' => 'nouveau task',
                'task[content]' => 'nouveau contenu Task'
            ]
        );
        $client->followRedirects();
        //Expected a redirection to tasks list
        $this->assertResponseRedirects('/tasks', 302);
        $this->assertNotNull($taskRepository->findOneBy(['title' => 'nouveau task']));
    }

    public function testEditTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $user = $userRepository->findOneByUsername('User9');
        $task = $taskRepository->findOneByTitle("task_3");
        //log a user       
        $client
            ->loginUser($user)
            ->request('GET', '/tasks/' . $task->getId() . '/edit');

        $client->submitForm(
            'Modifier',
            [
                'task[title]' => 'Task_3 modifier',
                'task[content]' => 'content Task troi modifier'
            ]
        );
        $client->followRedirects();
        //Expected a redirection to tasks list
        $this->assertResponseRedirects('/tasks', 302);
        $testTaskEdited = $taskRepository->find($task->getId());
        $this->assertSame('Task_3 modifier', $testTaskEdited->getTitle());
        
    }

    public function testToggleTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        //find a user
        $user = $userRepository->findOneByUsername('User4');
        //log a user
        $client->loginUser($user);
        /*$testTask = $user->getTasks()->first();
        $this->assertEquals(false, $testTask->isDone());*/
        $client->request('GET', '/tasks/' . 35 . '/toggle');
        $this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        /*$testTask = $taskRepository->findOneById($testTask->getId());
        $this->assertEquals(true, $testTask->isDone());*/
    }

    public function testDeleteTaskAuthorized(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        //Get a user
        $taskOwner = $userRepository->findOneByUsername('User6');
        //log a user
        $client->loginUser($taskOwner);
        //Get his own task
        $taskOwned = $taskOwner->getTasks()[0];
        $taskOwnedId = $taskOwned->getId();
        //Delete his own task
        $client->request('GET', '/tasks/' . $taskOwnedId . '/delete');
        //Expect a redirection to tasks list
        $client->followRedirects();
        $this->assertResponseRedirects('/tasks', 302);
        //Expect task is deleted
        $this->assertEquals(null, $taskRepository->findOneById($taskOwnedId));
    }

    public function testDeleteTaskAnonymous(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        //Get a user Admin
        $admin = $userRepository->findOneByUsername('Admin');
        //log a user
        $client->loginUser($admin);
        //Get an anonymous task
        $anonymousTask = $taskRepository->findOneByUser(null);
        $anonymousTaskId = $anonymousTask->getId();
        //Delete the anonymous task
        $client->request('GET', '/tasks/' . $anonymousTaskId . '/delete');
        //Expect a redirection to tasks list
        $client->followRedirects();
        $this->assertResponseRedirects('/tasks', 302);
        //Expect task is deleted
        $this->assertEquals(null, $taskRepository->findOneById($anonymousTaskId));
    }

    public function testDeleteTaskOfAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        //Get a user
        $admin = $userRepository->findOneByUsername('Admin');
        //log a user
        $client->loginUser($admin);
        //get id user "17"
        $task = $taskRepository->findOneByUser(17);
        $taskId = $task->getId();
        //Delete his own task

        $client->request('GET', '/tasks/' . $taskId . '/delete');
        //Expect a redirection to tasks list
        $client->followRedirects();
        $this->assertResponseRedirects('/tasks', 302);
        //Expect task is deleted
        $this->assertEquals(null, $taskRepository->findOneById($taskId));
    }
}
