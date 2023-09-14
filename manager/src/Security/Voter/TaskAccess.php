<?php

namespace App\Security\Voter;

use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Security\UserIdentity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TaskAccess extends Voter
{
	public const VIEW = 'view';
	public const    MANAGE = 'edit';
	public const DELETE = 'delete';

	private $security;

	public function __construct(AuthorizationCheckerInterface $security)
	{
		$this->security = $security;
	}

	protected function supports(string $attribute, mixed $subject): bool
	{
		return in_array($attribute, [self::VIEW, self::MANAGE, self::DELETE], true) && $subject instanceof Task;
	}

	protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();
		if (!$user instanceof UserIdentity) {
			return false;
		}

		if (!$subject instanceof Task) {
			return false;
		}

		return match ($attribute) {
			self::VIEW => $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS') ||
				$subject->getProject()->isMemberGranted(new Id($user->getId()), Permission::VIEW_TASKS),
			self::MANAGE => $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS') ||
				$subject->getProject()->isMemberGranted(new Id($user->getId()), Permission::MANAGE_TASKS),
			self::DELETE => $this->security->isGranted('ROLE_WORK_MANAGE_PROJECTS'),
			default => false,
		};

	}
}