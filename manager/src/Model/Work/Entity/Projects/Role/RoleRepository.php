<?php

namespace App\Model\Work\Entity\Projects\Role;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class RoleRepository
{
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $entityManager)
	{
		$this->repository = $entityManager->getRepository(Role::class);
	}

	public function get(string $id): Role
	{
		return $this->repository->findOneBy(['id' => $id]);
	}

	public function hasByName(string $name)
	{
		return $this->repository->findOneBy(['name' => $name]);
	}

	public function add(Role $role)
	{
		$this->entityManager->persist($role);
	}

	public function remove(Role $role)
	{
		$this->entityManager->remove($role);
	}
}