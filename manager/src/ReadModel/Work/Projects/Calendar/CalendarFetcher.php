<?php

namespace App\ReadModel\Work\Projects\Calendar;

use App\ReadModel\Work\Projects\Calendar\Query\Query;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;

class CalendarFetcher
{
    public function __construct(private Connection $connection)
    {
    }

    public function byMonth(Query $query): Result
    {
        $month = new DateTimeImmutable($query->year . '-' . $query->month . '-01');
        $start = $month->modify('-' . ($month->format('w') - 1) . 'days')->setTime(0, 0);
        $end = $start->modify('+34 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            't.id',
            't.name',
            'p.id as project_id',
            'p.name as project_name',
            'to_char(t.date, \'YYYY-MM-DD\') as date',
            't.plan_date',
            't.start_date',
            't.end_date'
        )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->where($qb->expr()->or(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('date');


        if ($query->project) {
            $qb->andWhere('t.project_id = :project_id')
                ->setParameter('project_id', $query->project);
        }

        if ($query->member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member_id')
                ->setParameter('member_id', $query->member);
        }

        $stmt = $qb->executeQuery()->fetchAllAssociative();

        return new Result($stmt, $start, $end, $month);
    }

    public function byWeek(DateTimeImmutable $now, string $member)
    {
        $start = $now->modify('-' . ($now->format('w') - 1) . 'days')->setTime(0, 0);
        $end = $start->modify('+6 days')->setTime(23, 59, 59);

        $qb = $this->connection->createQueryBuilder();

        $qb->select(
            't.id',
            't.name',
            'p.id as project_id',
            'p.name as project_name',
            'to_char(t.date, \'YYYY-MM-DD\') as date',
            't.plan_date',
            't.start_date',
            't.end_date'
        )
            ->from('work_projects_tasks', 't')
            ->leftJoin('t', 'work_projects_projects', 'p', 'p.id = t.project_id')
            ->where($qb->expr()->or(
                't.date BETWEEN :start AND :end',
                't.plan_date BETWEEN :start AND :end',
                't.start_date BETWEEN :start AND :end',
                't.end_date BETWEEN :start AND :end'
            ))
            ->setParameter('start', $start->format('Y-m-d'))
            ->setParameter('end', $end->format('Y-m-d'))
            ->orderBy('date');

        if ($member) {
            $qb->innerJoin('t', 'work_projects_project_memberships', 'ms', 't.project_id = ms.project_id');
            $qb->andWhere('ms.member_id = :member_id')
                ->setParameter('member_id', $member);
        }

        $stmt = $qb->executeQuery()->fetchAllAssociative();

        return new Result($stmt, $start, $end, $now);
    }
}