<?php

namespace App\ReadModel\Work\Projects\Task;

use App\ReadModel\Work\Projects\Task\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TaskFetcher
{
    public function __construct(private Connection $connection, private PaginatorInterface $paginator)
    {
    }

    public function all(Filter $filter, int $page, int $size, string $sort, string $direction): PaginationInterface|array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                't.author_id',
                'TRIM(CONCAT(wmm.name_first, \' \', wmm.name_last)) AS author_name',
                't.type',
                't.name',
                't.progress',
                't.project_id',
                't.parent_id AS parent',
                'p.name as project_name',
                't.plan_date AS plan',
                't.status',
                't.priority',
            )
            ->from('work_projects_tasks', 't')
            ->innerJoin('t', 'work_projects_projects', 'p', 't.project_id = p.id')
            ->innerJoin('t', 'work_members_members', 'wmm', 't.author_id = wmm.id');

        if ($filter->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member');
            $qb->setParameter('member', $filter->member);
        }

        if ($filter->project) {
            $qb->andWhere('t.project_id = :project')
                ->setParameter('project', $filter->project);
        }

        if ($filter->type) {
            $qb->andWhere('type = :type');
            $qb->setParameter('type', $filter->type);
        }

        if ($filter->status) {
            $qb->andWhere('t.status = :status');
            $qb->setParameter('status', $filter->status);
        }

        if ($filter->priority) {
            $qb->andWhere('priority = :priority');
            $qb->setParameter('priority', $filter->priority);
        }

        if ($filter->executor) {
            $qb->innerJoin('t', 'work_projects_tasks_executors', 'e', 'e.task_id = t.id');
            $qb->andWhere('e.member_id = :executor');
            $qb->setParameter('executor', $filter->executor);
        }

        if (!\in_array($sort, ['id', 'type', 'status', 'priority', 'executor'], true)) {
            throw new \UnexpectedValueException('Cannot sort by ' . $sort);
        }


        $qb->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        $pagination = $this->paginator->paginate($qb, $page, $size);
        $tasks = (array)$pagination->getItems();
        $executors = $this->executorsForTasks(array_column($tasks, 'id'));

        $pagination->setItems(array_map(static function (array $task) use ($executors): array {
            $taskExecutors = array_filter($executors, static function (array $executor) use ($task): bool {
                return $executor['task_id'] === $task['id'];
            });

            return [
                ...$task,
                'executors' => $taskExecutors,
            ];
        }, $tasks));

        return $pagination;
    }

    public function executorsForTasks(array $ids): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'e.task_id',
                'TRIM(CONCAT(m.name_first, \' \', m.name_last)) AS name'
            )
            ->from('work_projects_tasks_executors', 'e')
            ->innerJoin('e', 'work_members_members', 'm', 'm.id = e.member_id')
            ->andWhere('e.task_id IN (:task)')
            ->setParameter('task', $ids, Connection::PARAM_INT_ARRAY)
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAllAssociative();
    }

    public function childrenOf(int $task)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.date',
                't.project_id',
                'p.name as project_name',
                't.name',
                't.parent_id as parent',
                't.type',
                't.priority',
                't.progress',
                't.plan_date',
                't.status',
            )
            ->from('work_projects_tasks', 't')
            ->innerJoin('t', 'work_projects_projects', 'p', 't.project_id = p.id')
            ->andWhere('t.parent_id = :parent')
            ->setParameter('parent', $task)
            ->orderBy('date', 'desc')
            ->executeQuery();

        $tasks = $stmt->fetchAllAssociative();
        $executors = $this->executorsForTasks(array_column($tasks, 'id'));

        return array_map(static function (array $task) use ($executors) {
            $taskExecutors = array_filter($executors, static function (array $executor) use ($task) {
                return $executor['task_id'] === $task['id'];
            });

            return [
                ...$task,
                'executors' => $taskExecutors,
            ];
        }, $tasks);
    }
}