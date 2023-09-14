<?php

namespace App\Model\Work\Entity\Projects\Task\File;

use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Task\Task;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
#[ORM\Table(name: 'work_projects_task_files')]
#[ORM\Index(columns: ['date'])]
class File
{
    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'departments')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id', nullable: false)]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'member_id', referencedColumnName: 'id', nullable: false)]
    private Member $member;

    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_task_file_id')]
    private Id $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Embedded(class: Info::class)]
    private Info $info;

    public function __construct(Task $task, Id $id, Member $member, Info $info, DateTimeImmutable $date)
    {
        $this->task = $task;
        $this->id = $id;
        $this->member = $member;
        $this->info = $info;
        $this->date = $date;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @return Member
     */
    public function getMember(): Member
    {
        return $this->member;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }
}