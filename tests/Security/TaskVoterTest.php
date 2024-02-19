<?php

namespace App\tests\Security;

use App\Entity\Task;
use App\Security\Voter\TaskVoter;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\TestBrowserToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;


class TaskVoterTest extends KernelTestCase
{

    public function testWithNoAuth()
    {
        self::bootKernel();

        $voter = static::getContainer()->get(TaskVoter::class);
        
        $token = new TestBrowserToken(['ROLE_USER'], null, 'main');

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, new Task(), [TaskVoter::DELETE]));
    }

    public function testDeleteTaskByAdmin()
    {
        self::bootKernel();

        $voter = static::getContainer()->get(TaskVoter::class);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findOneByUsername("Admin");

        $token = new PreAuthenticatedToken($admin, 'main', $admin->getRoles());
        //generate an anonymous task
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, new Task(), [TaskVoter::DELETE]));
    }

    public function testDeleteTaskByTheTaskOwner()
    {
        self::bootKernel();

        $voter = static::getContainer()->get(TaskVoter::class);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneByUsername("User1");

        $taskOwned = $user->getTasks()[0];

        $token = new PreAuthenticatedToken($user, 'main', $user->getRoles());

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $taskOwned, [TaskVoter::DELETE]));
    }

    public function testDeleteTaskByTheWrongTaskOwner()
    {
        self::bootKernel();

        $voter = static::getContainer()->get(TaskVoter::class);

        $userRepository = static::getContainer()->get(UserRepository::class);

        $wrongUser = $userRepository->findOneByUsername("User1");

        $token = new PreAuthenticatedToken($wrongUser, 'main', $wrongUser->getRoles());

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $voter->vote($token, new Task(), [TaskVoter::DELETE]));
    }
}
