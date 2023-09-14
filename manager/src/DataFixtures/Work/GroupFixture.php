<?php

namespace App\DataFixtures\Work;

use App\Model\Work\Entity\Members\Group\Group;
use App\Model\Work\Entity\Members\Group\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GroupFixture extends Fixture
{
	public const REFERENCE_STAFF = 'work_member_group_staff';
	public const REFERENCE_CUSTOMERS = 'work_member_group_customers';

	public function load(ObjectManager $manager): void
	{
		$group = new Group(
			Id::next(),
			'Our Staff'
		);
		$this->setReference(self::REFERENCE_STAFF, $group);
		$manager->persist($group);

		$group = new Group(
			Id::next(),
			'Customers'
		);
		$this->setReference(self::REFERENCE_CUSTOMERS, $group);
		$manager->persist($group);

		$manager->flush();
	}
}