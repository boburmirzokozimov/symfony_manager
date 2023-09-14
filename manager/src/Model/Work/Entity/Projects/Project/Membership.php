<?php

namespace App\Model\Work\Entity\Projects\Project;


use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use App\Model\Work\Entity\Projects\Role\Role;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\Uuid;
use function count;

#[ORM\Table(name: 'work_projects_project_memberships')]
#[ORM\UniqueConstraint(columns: ['project_id', 'member_id'])]
#[ORM\Entity()]
class Membership
{
	#[ORM\Id]
	#[ORM\Column(type: 'guid')]
	private string $id;

	#[ORM\ManyToOne(targetEntity: 'Project', inversedBy: 'memberships')]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: false)]
	private Project $project;

	#[ORM\ManyToOne(targetEntity: 'App\Model\Work\Entity\Members\Member\Member')]
	#[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
	private Member $member;

	#[ORM\ManyToMany(targetEntity: 'App\Model\Work\Entity\Projects\Project\Department\Department')]
	#[ORM\JoinTable(name: 'work_projects_project_membership_departments')]
	#[ORM\JoinColumn(name: 'membership_id', referencedColumnName: 'id')]
	#[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id')]
	private $departments;

	#[ORM\ManyToMany(targetEntity: 'App\Model\Work\Entity\Projects\Role\Role')]
	#[ORM\JoinTable(name: 'work_projects_project_membership_roles')]
	#[ORM\JoinColumn(name: 'membership_id', referencedColumnName: 'id')]
	#[ORM\InverseJoinColumn(name: 'role_id', referencedColumnName: 'id')]
	private $roles;

	public function __construct(Project $project, Member $member, array $departments, array $roles)
	{
		$this->guardDepartments($departments);
		$this->guardRoles($roles);
		$this->id = Uuid::uuid4()->toString();
		$this->project = $project;
		$this->member = $member;
		$this->departments = new ArrayCollection($departments);
		$this->roles = new ArrayCollection($roles);
	}

	private function guardDepartments(array $departments)
	{
		if (count($departments) === 0) {
			throw new \DomainException('Set at least one department');
		}
	}

	private function guardRoles(array $roles)
	{
		if (count($roles) === 0) {
			throw new \DomainException('Set at least one role');
		}
	}

	public function getId(): string
	{
		return $this->id;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getMember(): Member
	{
		return $this->member;
	}

	public function getDepartments(): array
	{
		return $this->departments->toArray();
	}

	public function getRoles(): array
	{
		return $this->roles->toArray();
	}

	public function isGranted(string $permissions)
	{
		foreach ($this->roles as $role) {
			if ($role->hasPermission($permissions)) {
				return true;
			}
		}
		return false;
	}

	#[Pure] public function isForMember(MemberId $id): bool
	{
		return $this->member->getId()->isEqual($id);
	}

	#[Pure] public function isForDepartment(DepartmentId $id): bool
	{
		foreach ($this->departments as $department) {
			if ($department->getId()->isEqual($id)) {
				return true;
			}
		}
		return false;
	}

	public function changeDepartments(array $departments): void
	{
		$this->guardDepartments($departments);

		$current = $this->departments->toArray();
		$new = $departments;
		$compare = static function (Department $a, Department $b): int {
			return $a->getId()->getValue() <=> $b->getId()->getValue();
		};

		foreach (array_udiff($current, $new, $compare) as $item) {
			$this->departments->removeElement($item);
		}

		foreach (array_udiff($new, $current, $compare) as $item) {
			$this->departments->add($item);
		}
	}

	public function changeRoles(array $roles): void
	{
		$this->guardRoles($roles);

		$current = $this->roles->toArray();
		$new = $roles;

		$compare = static function (Role $a, Role $b): int {
			return $a->getId()->getValue() <=> $b->getId()->getValue();
		};

		foreach (array_udiff($current, $new, $compare) as $item) {
			$this->roles->removeElement($item);
		}

		foreach (array_udiff($new, $current, $compare) as $item) {
			$this->roles->add($item);
		}
	}
}