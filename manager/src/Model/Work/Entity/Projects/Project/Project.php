<?php

namespace App\Model\Work\Entity\Projects\Project;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Department\Department;
use App\Model\Work\Entity\Projects\Project\Department\Id as DepartmentId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use JetBrains\PhpStorm\Pure;

#[ORM\Table(name: 'work_projects_projects')]
#[ORM\Entity()]
class Project
{
	#[ORM\Id]
	#[ORM\Column(type: 'work_projects_project_id')]
	private Id $id;

	#[ORM\Column(type: 'string')]
	private string $name;

	#[ORM\Column(type: 'work_projects_project_status')]
	private Status $status;

	#[ORM\Column(type: 'integer')]
	private int $sort;

	/**
	 * @var ArrayCollection|Department[]
	 */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: 'App\Model\Work\Entity\Projects\Project\Department\Department', cascade: ['all'], orphanRemoval: true)]
	#[ORM\OrderBy(['name' => 'ASC'])]
	private $departments;

	/**
	 * @var ArrayCollection|Membership[]
	 */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: Membership::class, cascade: ['all'], orphanRemoval: true)]
	private $memberships;

	public function __construct(Id $id, string $name, int $sort)
	{
		$this->id = $id;
		$this->name = $name;
		$this->sort = $sort;
		$this->status = Status::active();
		$this->departments = new ArrayCollection();
		$this->memberships = new ArrayCollection();
	}

	public function addMember(Member $member, array $departmentIds, array $roles)
	{
		foreach ($this->memberships as $membership) {
			if ($membership->isForMember($member->getId())) {
				throw new DomainException('Member already exists');
			}
		}
		$departments = array_map([$this, 'getDepartment'], $departmentIds);
		$this->memberships->add(new Membership($this, $member, $departments, $roles));
	}

	public function editMember(MemberId $memberId, array $departments, array $roles)
	{
		foreach ($this->memberships as $membership) {
			if ($membership->isForMember($memberId)) {
				$membership->changeDepartments(array_map([$this, 'getDepartment'], $departments));
				$membership->changeRoles($roles);
				return;
			}
		}
		throw new DomainException('Member was not found');
	}

	public function isMemberGranted(MemberId $memberId, string $permissions)
	{
		foreach ($this->memberships as $membership) {
			if ($membership->isForMember($memberId)) {
				return $membership->isGranted($permissions);
			}
		}
	}

	public function removeMember(MemberId $id)
	{
		foreach ($this->memberships as $membership) {
			if ($membership->isForMember($id)) {
				$this->memberships->removeElement($membership);
				return;
			}
		}
		throw new DomainException('Member was not found');
	}

	public function getDepartment(DepartmentId $id): Department
	{
		foreach ($this->departments as $department) {
			if ($department->isEqual($id)) {
				return $department;
			}
		}
		throw new DomainException('Department is not found');
	}

	public function addDepartment(DepartmentId $departmentId, string $name): void
	{
		foreach ($this->departments as $department) {
			if ($department->isNameEqual($name)) {
				throw new DomainException('Department with this name already exists');
			}
		}
		$this->departments->add(new Department($this, $departmentId, $name));
	}

	public function removeDepartment(DepartmentId $departmentId): void
	{
		foreach ($this->departments as $department) {
			if ($department->isEqual($departmentId)) {
				foreach ($this->memberships as $membership) {
					if ($membership->isForDepartment($departmentId)) {
						throw new DomainException('Unable to remove the department with members');
					}
				}
				$this->departments->removeElement($department);
				return;
			}
		}
		throw new DomainException('Department is not found');
	}

	public function editDepartment(DepartmentId $departmentId, string $name): void
	{
		foreach ($this->departments as $department) {
			if ($department->isEqual($departmentId)) {
				$department->edit($name);
				return;
			}
		}
		throw new DomainException('Department is not found');
	}

	#[Pure] public function getDepartments()
	{
		return $this->departments->toArray();
	}

	public function edit(string $name, int $sort)
	{
		$this->name = $name;
		$this->sort = $sort;
	}

	public function archive()
	{
		if ($this->status->isArchived()) {
			throw new DomainException('Project is already archived');
		}
		$this->status = Status::archive();
	}

	public function reinstate()
	{
		if ($this->status->isActive()) {
			throw new DomainException('Project is already active');
		}
		$this->status = Status::active();
	}

	public function getId(): Id
	{
		return $this->id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getStatus(): Status
	{
		return $this->status;
	}

	public function getSort(): int
	{
		return $this->sort;
	}

	#[Pure] public function getMemberships(): array
	{
		return $this->memberships->toArray();
	}

	public function hasMember(MemberId $userId): bool
	{
		foreach ($this->memberships as $member) {
			if ($member->isForMember($userId)) {

			}
		}
	}
}