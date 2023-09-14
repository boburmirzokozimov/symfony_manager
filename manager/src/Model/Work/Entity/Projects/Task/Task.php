<?php

namespace App\Model\Work\Entity\Projects\Task;

use App\Model\AggregateRoot;
use App\Model\EventsTrait;
use App\Model\Work\Entity\Members\Member\Id;
use App\Model\Work\Entity\Members\Member\Member;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\Entity\Projects\Task\Event\TaskExecutorAssigned;
use App\Model\Work\Entity\Projects\Task\File\File;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\File\Info;
use App\Model\Work\Entity\Projects\Task\Id as TaskId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;
use JetBrains\PhpStorm\Pure;
use Webmozart\Assert\Assert;

#[ORM\Entity()]
#[ORM\Table(name: 'work_projects_tasks')]
#[ORM\Index(columns: ['date'])]
class Task implements AggregateRoot
{
    use EventsTrait;

    #[ORM\Column(type: 'smallint')]
    public int $progress;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    public ?DateTimeImmutable $planDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    public ?DateTimeImmutable $startDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    public ?DateTimeImmutable $endDate = null;

    #[ORM\ManyToOne(targetEntity: Task::class)]
    #[ORM\JoinColumn(name: 'parent_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    public ?Task $parent;

    #[ORM\Column(type: 'work_projects_tasks_status', length: 16)]
    public Status $status;

    #[ORM\ManyToMany(targetEntity: Member::class)]
    #[ORM\JoinTable(name: 'work_projects_tasks_executors')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'member_id', referencedColumnName: 'id')]
    #[ORM\OrderBy(['name.first' => 'ASC'])]
    public $executors;

    #[ORM\Id]
    #[ORM\Column(type: 'work_projects_tasks_id')]
    #[ORM\GeneratedValue(strategy: 'NONE')]
    #[ORM\SequenceGenerator(sequenceName: 'work_projects_tasks_seq', initialValue: 1)]
    private TaskId $id;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: "project_id", referencedColumnName: 'id', nullable: false)]
    private Project $project;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: "author_id", referencedColumnName: 'id', nullable: false)]
    private Member $author;

    #[ORM\OneToMany(mappedBy: 'task', targetEntity: File::class, cascade: ['all'], orphanRemoval: true)]
    private $files;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $date;

    #[ORM\Column(type: 'work_projects_tasks_type', length: 16)]
    private Type $type;

    #[ORM\Column(type: 'smallint')]
    private int $priority;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $content;

    public function __construct(TaskId            $id,
                                Project           $project,
                                Member            $author,
                                DateTimeImmutable $date,
                                Type              $type,
                                int               $priority,
                                string            $name,
                                ?string           $content)
    {
        $this->id = $id;
        $this->progress = 0;
        $this->status = Status::new();
        $this->executors = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->project = $project;
        $this->author = $author;
        $this->date = $date;
        $this->type = $type;
        $this->priority = $priority;
        $this->name = $name;
        $this->content = $content;
    }

    public function assignExecutor(Member $executor)
    {
        if ($this->executors->contains($executor)) {
            throw new DomainException('Executor ' . $executor->getName()->getName() . ' is already assigned');
        }
        $this->executors->add($executor);
        $this->recordEvent(new TaskExecutorAssigned($this->id, $executor->getId()));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    #[Pure] public function hasExecutor(Id $id): bool
    {
        foreach ($this->executors->toArray() as $executor) {
            if ($executor->getId()->isEqual($id)) {
                return true;
            }
        }
        return false;
    }

    public function revokeExecutor(Member $id): void
    {
        foreach ($this->executors as $executor) {
            if ($executor->getId()->isEqual($id->getId())) {
                $this->executors->removeElement($executor);
                return;
            }
        }
        throw new DomainException('Executor is not assigned ');
    }

    public function start(DateTimeImmutable $date)
    {
        if (!$this->isNew()) {
            throw new DomainException('Task is already started');
        }
        if (!$this->executors->count()) {
            throw new DomainException('First assign executors');
        }
        $this->changeStatus(Status::working(), $date);
    }

    #[Pure] public function isNew(): bool
    {
        return $this->status->isNew();
    }

    public function changeStatus(Status $status, DateTimeImmutable $date): void
    {
        if ($this->status->isEqual($status)) {
            throw  new DomainException('Status is already the same');
        }

        $this->setStatus($status);

        if (!$status->isNew() && !$this->startDate) {
            $this->startDate = $date;
        }
        if ($status->isDone()) {
            if ($this->progress !== 100) {
                $this->changeProgress(100);
            }
            $this->endDate = $date;
        } else {
            $this->endDate = $date;
        }
    }

    public function changeProgress(int $progress): void
    {
        Assert::range($progress, 1, 100);
        {
            if ($progress == $this->progress) {
                throw new DomainException('Progress is already the same');
            }
            $this->setProgress($progress);
        }
    }

    public function changePriority(int $priority): void
    {
        Assert::range($priority, 1, 4);
        if ($priority == $this->priority) {
            throw  new DomainException('Priority is already the same ');
        }
        $this->setPriority($priority);
    }

    public function getStatus(): Status
    {
        return $this->status;
    }

    public function setStatus(Status $status): void
    {
        $this->status = $status;
    }

    public function edit(string $name, string $content): void
    {
        $this->setName($name);
        $this->setContent($content);
    }

    public function plan(?DateTimeImmutable $date): void
    {
        $this->setPlanDate($date);
    }

    public function move(Project $project): void
    {
        if ($project === $this->getProject()) {
            throw new DomainException('Project is already the same');
        }
        $this->setProject($project);
    }

    public function getProject(): Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function changeType(Type $type): void
    {
        if ($this->type->isEqual($type)) {
            throw new DomainException('Type is already the same');
        }
        $this->setType($type);
    }

    public function setChildOf(?Task $parent): void
    {
        if ($parent) {
            $current = $parent;
            do {
                if ($current === $this) {
                    throw new DomainException('Cyclomatic children');
                }
            } while ($current && $current = $current->getParent());
        }

        $this->setParent($parent);
    }

    public function getParent(): ?Task
    {
        return $this->parent;
    }

    public function setParent(?Task $parent): void
    {
        $this->parent = $parent;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): void
    {
        $this->progress = $progress;
    }

    public function getAuthor(): Member
    {
        return $this->author;
    }

    public function setAuthor(Member $author): void
    {
        $this->author = $author;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function setType(Type $type): void
    {
        $this->type = $type;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getPlanDate(): ?DateTimeImmutable
    {
        return $this->planDate;
    }

    public function setPlanDate(?DateTimeImmutable $planDate): void
    {
        $this->planDate = $planDate;
    }

    public function addFile(FileId $id, Member $member, DateTimeImmutable $date, Info $info): void
    {
        $this->files->add(new File($this, $id, $member, $info, $date));
    }

    public function getFiles(): array
    {
        return $this->files->toArray();
    }

    public function removeFile(string $id): void
    {
        /** @var File $file */
        foreach ($this->files->toArray() as $file) {
            if ($file->getId()->isEqual($id)) {
                $this->files->removeElement($file);
                return;
            }
        }
        throw new DomainException('File was not found');

    }
}