<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, $task): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::DELETE])
            && $task instanceof \App\Entity\Task;
    }

    protected function voteOnAttribute(string $attribute, $task, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // Tasks can only be deleted by users who created the task in question.
        if ($user->getRoles()[0] === "ROLE_ADMIN"){
            return true;
        }
        // Tasks attached to the “anonymous” user can only be deleted by users with the administrator role (ROLE_ADMIN).
        if ($user === $task->getUser() && $task->getUser() !== null){
            return true;
        }

        return false;
    }
}
