<?php

namespace App\DataFixtures\Work;

use App\DataFixtures\AppFixtures;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Member\Email;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Members\Member\Name;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MemberFixture extends Fixture implements DependentFixtureInterface
{
	public function load(ObjectManager $manager)
	{
		/** @var User $admin */
		/** @var User $user */
		$admin = $this->getReference(AppFixtures::REFERENCE_ADMIN);
		$user = $this->getReference(AppFixtures::REFERENCE_USER);

		/** @var Group $staff */
		/** @var Group $customers */
		$staff = $this->getReference(GroupFixture::REFERENCE_STAFF);
		$customers = $this->getReference(GroupFixture::REFERENCE_CUSTOMERS);

		$member = $this->createMember($admin, $staff);
		$manager->persist($member);

		$member = $this->createMember($user, $customers);
		$manager->persist($member);

		$manager->flush();
	}

	public function createMember(User $user, Group $group)
	{
		return new Member(
			new Id($user->getId()->getValue()),
			new Name($user->getName()->getFirst(), $user->getName()->getLast()),
			new Email($user->getEmail()),
			$group
		);
	}

	public function getDependencies()
	{
		return [
			AppFixtures::class,
			GroupFixture::class,
		];
	}
}