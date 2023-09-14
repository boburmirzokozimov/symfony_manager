<?php

namespace App\Model\Work\UseCase\Projects\Task\Files\Remove;

use App\Model\Flusher;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;

class Handler
{
    public function __construct(private TaskRepository $taskRepository,
                                private Flusher        $flusher)
    {
    }

    public function handle(Command $command): void
    {
        $task = $this->taskRepository->get(new Id($command->task));

        $task->removeFile($command->id);

        $this->flusher->flush();
    }
} 