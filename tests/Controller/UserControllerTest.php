<?php

namespace App\tests\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testListNoAuth(): void
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/users');
        //expected a redirection
        $this->assertResponseRedirects();
        $client->followRedirect();
        //expected a redirection to /login
        $this->assertRouteSame('app_login');
    }

    public function testListUser(): Void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('User1');

        $client
            ->loginUser($user)
            ->request(Request::METHOD_GET, '/users');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testListAdmin(): Void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByUsername('Admin');

        $client
            ->loginUser($user)
            ->request(Request::METHOD_GET, '/users');

        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');
    }

    public function testUserCreateByAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $Admin = $userRepository->findOneByUsername("Admin");
        //log an Admin and go to add a user page
        $client
            ->loginUser($Admin)
            ->request('GET', '/users/create');
        //fill the form with rights datas
        $client->submitForm(
            'Ajouter',
            [
                'user[username]' => 'IlyesBHA',
                'user[password][first]' => 'password',
                'user[password][second]' => 'password',
                'user[email]' => 'ilyesbha@gmail.fr',
                'user[roles]' => 'ROLE_USER'
            ]
        );
        //confirm the redirection
        $client->followRedirects();
        $this->assertResponseRedirects('/users', 302);
    }

    public function testUserEditByAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $Admin = $userRepository->findOneByUsername("Admin");
        $User = $userRepository->findOneByUsername("User22");

        $client
            ->loginUser($Admin)
            ->request('GET', '/users/' . $User->getId() . '/edit');

        $client->submitForm(
            'Modifier',
            [
                'user[username]' => 'User_2',
                'user[password][first]' => 'password',
                'user[password][second]' => 'password',
                'user[email]' => 'user2@gmail.fr',
                'user[roles]' => 'ROLE_USER'
            ]
        );
        //confirm the redirection
        $client->followRedirects();
        $this->assertResponseRedirects('/users', 302);
        //get the user edited by his id
        $testUserEdited = $userRepository->find($User->getId());
        //confirm the email changed in the database
        $this->assertNotNull($userRepository->findOneBy(['email' => 'user2@gmail.fr']));
        //confirm the old email was deleted
        $this->assertNull($userRepository->findOneBy(['email' => 'user22@gmail.fr']));
        //confirm the new username
        $this->assertSame('User_2', $testUserEdited->getUsername());
    }
}
