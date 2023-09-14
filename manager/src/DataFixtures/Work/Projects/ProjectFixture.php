<?php

namespace App\DataFixtures\Work\Projects;

use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Project\Id;
use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixture extends Fixture
{
	public function load(ObjectManager $manager)
	{
		$active = $this->createProject('First project', 1);
		$active->addDepartment(DepartmentId::next(), 'Marketing');
		$active->addDepartment(DepartmentId::next(), 'Development');
		$manager->persist($active);

		$active = $this->createProject('Second project', 2);
		$manager->persist($active);

		$archived = $this->createArchivedProject('Third project', 3);
		$manager->persist($archived);

		$manager->flush();
	}

	private function createProject(string $name, int $sort): Project
	{
		return new Project(
			Id::next(),
			$name,
			$sort,
		);
	}

	private function createArchivedProject(string $name, int $sort): Project
	{
		$archived = $this->createProject($name, $sort);
		$archived->archive();
		return $archived;
	}
}