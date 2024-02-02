<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function testLoginwithGoodCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        /**
         * test login with good credentials
         */
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'User1',
            'password' => 'password']);
        $client->submit($form);
        $client->followRedirect();
        $this->assertRouteSame('homepage');
    }

    public function testLoginWithWrongCredentials(): void
    {
        $client = static::createClient();
        $crawler = $client->request(Request::METHOD_GET, '/login');
        /**
         * test login with wrong credentials
         */
        $form = $crawler->selectButton('Se connecter')->form([
            'username' => 'khoukha@doe.fr',
            'password' => '123password']);
        $client->submit($form);
        //$this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public static function login(KernelBrowser $client, string $name): ?User
    {
        try {
            $user = static::getContainer()->get(UserRepository::class)->findOneByUsername($name);
            $client->loginUser($user);

            return $user;
        } catch (\Exception $e) {
        }

        return null;
    }
    
}