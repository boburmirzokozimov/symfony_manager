<?php

namespace App\Model\Work\Entity\Projects\Task;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TaskRepository
{
	private Connection $connection;
	private EntityRepository $repository;

	public function __construct(private EntityManagerInterface $em)
	{
		$this->connection = $em->getConnection();
		$this->repository = $em->getRepository(Task::class);
	}

	public function add(Task $task): void
	{
		$this->em->persist($task);
	}

	public function nextId(): Id
	{
		return new Id((int)$this->connection->executeQuery('SELECT nextval(\'work_projects_tasks_seq\')')->fetchOne());
	}

	public function get(Id $id): Task
	{
		return $this->repository->find($id);
	}

	public function remove(Task $task)
	{
		$this->em->remove($task);
	}

	public function allByParent(Id $getId): array
	{
		return $this->repository->findBy(['parent' => $getId->getValue()]);
	}
}