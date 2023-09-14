<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Model\Comment\Entity\Comment\Comment;
use App\ReadModel\Comment\CommentView;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentAccess extends Voter
{
    public const MANAGE = 'manage';

    public function __construct(private AuthorizationCheckerInterface $security)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::MANAGE && ($subject instanceof Comment || $subject instanceof CommentView);
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::MANAGE => $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS'),
            default => false,
        };

    }
}