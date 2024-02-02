<?php 

namespace App\tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{

    public function testCount(){

        self::bootKernel();
        $container = static::getContainer();

        $users = $container->get(UserRepository::class)->count([]);
        $this->assertEquals(11, $users);
    }


}