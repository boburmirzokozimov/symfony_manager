<?php

namespace App\Model\Work\UseCase\Projects\Task\Files\Add;

use App\Model\Flusher;
use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\File\Id as FileId;
use App\Model\Work\Entity\Projects\Task\File\Info;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use DateTimeImmutable;

class Handler
{
    public function __construct(private TaskRepository   $taskRepository,
                                private MemberRepository $memberRepository,
                                private Flusher          $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->id));
        $member = $this->memberRepository->get(new MemberId($command->member));

        $files = $command->files;

        /** @var \App\Service\Uploader\File $file */
        foreach ($files as $file) {
            $task->addFile(
                FileId::next(),
                $member,
                new DateTimeImmutable(),
                new Info(
                    $file->getPath(),
                    $file->getName(),
                    $file->getSize()
                )
            );
        }

        $this->flusher->flush();
    }
}