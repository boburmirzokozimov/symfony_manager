<?php

namespace App\Model\Work\Entity\Projects\Project\Department;

use App\Model\Work\Entity\Projects\Project\Project;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'work_projects_project_departments')]
#[ORM\Entity()]
class Department
{
	#[ORM\Id]
	#[ORM\Column(type: 'work_projects_department_id')]
	private Id $id;

	#[ORM\Column(type: 'string')]
	private string $name;

	#[ORM\ManyToOne(targetEntity: 'App\Model\Work\Entity\Projects\Project\Project', inversedBy: 'departments')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
	private Project $project;

	public function __construct(Project $project, Id $id, string $name)
	{
		$this->project = $project;
		$this->id = $id;
		$this->name = $name;
	}

	#[Pure] public function isEqual(string $id): bool
	{
		return $this->getId()->getValue() == $id;
	}

	public function getId(): Id
	{
		return $this->id;
	}

	public function edit(string $name): void
	{
		$this->name = $name;
	}

	public function isNameEqual($other): bool
	{
		return $this->name === $other;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getProject(): Project
	{
		return $this->project;
	}
}