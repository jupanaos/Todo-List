<?php

namespace App\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';

    public function __construct(private Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Task;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        if (!$subject instanceof Task) {
            throw new \Exception('Ceci n\est pas une tâche !');
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::EDIT:
                // logic to determine if the user can EDIT
                return $user === $subject->getAuthor();
                // logic to determine if the user can DELETE task (author or admin)
            case self::DELETE:
                return $this->canDelete($token->getUser(), $subject);
        }

        return false;
    }

    private function canDelete(?User $user, mixed $subject): bool
    {
        return $subject->getAuthor() === $user || (in_array('ROLE_ANONYMOUS', $subject->getAuthor()->getRoles()) && $this->security->isGranted('ROLE_ADMIN'));
    }
}
