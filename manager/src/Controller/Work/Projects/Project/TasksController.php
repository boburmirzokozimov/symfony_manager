<?php

namespace App\Controller\Work\Projects\Project;

use App\Controller\ErrorHandler;
use App\Model\Comment\UseCase\Comment\Create\Command;
use App\Model\Comment\UseCase\Comment\Create\Form as CommentForm;
use App\Model\Comment\UseCase\Comment\Create\Handler;
use App\Model\Work\Entity\Projects\Task\File\File as FileEntity;
use App\Model\Work\Entity\Projects\Task\Task as TaskEntity;
use App\Model\Work\UseCase\Projects\Task;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Task\CommentFetcher;
use App\ReadModel\Work\Projects\Task\Filter\Filter;
use App\ReadModel\Work\Projects\Task\Filter\Form;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\Security\Voter\TaskAccess;
use App\Service\Uploader\File;
use App\Service\Uploader\FileUploader;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/tasks', name: 'work.projects.tasks')]
class TasksController extends AbstractController
{
    private const PER_PAGE = 50;

    public function __construct(private ErrorHandler $errorHandler)
    {
    }

    #[Route(path: '', name: '')]
    public function index(Request $request, TaskFetcher $fetcher): Response
    {
        if ($this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $filter = Filter::all();
        } else {
            $filter = Filter::all()->forMember($this->getUser()->getId());
        }

        $form = $this->createForm(Form::class, $filter);
        $form->handleRequest($request);

        $pagination = $fetcher->all(
            $filter,
            $request->query->getInt('page', 1),
            self::PER_PAGE,
            $request->query->get('sort', 'id'),
            $request->query->get('direction', 'desc')
        );

        return $this->render('app/work/projects/task/index.html.twig', [
            'project' => null,
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{id}/edit', name: '.edit')]
    public function edit(TaskEntity $task, Request $request, Task\Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Task\Edit\Command::fromCommand($task);

        $form = $this->createForm(Task\Edit\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route(path: '/{id}/assign', name: '.assign')]
    public function assign(TaskEntity $task, Request $request, Task\Executor\Assign\Handler $handler): Response
    {
        $project = $task->getProject();
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Task\Executor\Assign\Command::fromCommand($task, $task->executors->toArray());

        $form = $this->createForm(Task\Executor\Assign\Form::class, $command, ['project_id' => $project->getId()->getValue()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/assign.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route(path: '/{id}/child', name: '.child')]
    public function childOf(TaskEntity $task, Request $request, Task\ChildOf\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Task\ChildOf\Command::fromCommand($task);

        $form = $this->createForm(Task\ChildOf\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/child.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route(path: '/{id}/move', name: '.move')]
    public function move(TaskEntity $task, Request $request, Task\Move\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Task\Move\Command::fromCommand($task);

        $form = $this->createForm(Task\Move\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/move.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route(path: '/{id}/plan/set', name: '.plan.set')]
    public function plan(TaskEntity $task, Request $request, Task\Plan\Set\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = Task\Plan\Set\Command::fromCommand($task);

        $form = $this->createForm(Task\Plan\Set\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.tasks');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/plan.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ]);
    }

    #[Route(path: '/{id}/plan/remove', name: '.plan.remove')]
    public function planRemove(TaskEntity $task, Request $request, Task\Plan\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove-plan', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\Plan\Remove\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('work.projects.tasks.show', [
            'id' => $task->getId()->getValue()
        ]);
    }

    #[Route(path: '/{id}/remove', name: '.remove')]
    public function remove(TaskEntity $task, Request $request, Task\Remove\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::DELETE, $task);

        $command = new Task\Remove\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('work.projects.tasks');
    }

    #[Route(path: '/{id}/start', name: '.start')]
    public function start(TaskEntity $task, Request $request, Task\Start\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('start', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\Start\Command($task->getId()->getValue());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', [
            'id' => $task->getId()->getValue()
        ]);
    }

    #[Route(path: '/{id}/take', name: '.take')]
    public function take(TaskEntity $task, Request $request, Task\Take\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('take', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\Take\Command($task->getId()->getValue(), $this->getUser()->getId());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', [
            'id' => $task->getId()->getValue()
        ]);
    }

    #[Route(path: '/{id}/revoke/{member_id}', name: '.revoke')]
    public function revoke(TaskEntity $task, string $member_id, Request $request, Task\Executor\Revoke\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\Executor\Revoke\Command($task->getId()->getValue(), $member_id);

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', [
            'id' => $task->getId()->getValue()
        ]);
    }

    #[Route(path: '/{id}/take/start', name: '.take_and_start')]
    public function takeAndStart(TaskEntity $task, Request $request, Task\TakeAndStart\Handler $handler): Response
    {
        if (!$this->isCsrfTokenValid('take-and-start', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\TakeAndStart\Command($task->getId()->getValue(), $this->getUser()->getId());

        try {
            $handler->handle($command);
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.show', [
            'id' => $task->getId()->getValue()
        ]);
    }

    #[Route(path: '/{id}/show', name: '.show')]
    public function show(TaskEntity            $task,
                         Request               $request,
                         MemberFetcher         $memberFetcher,
                         TaskFetcher           $taskFetcher,
                         CommentFetcher        $commentFetcher,
                         Task\Status\Handler   $statusHandler,
                         Task\Progress\Handler $progressHandler,
                         Task\Type\Handler     $typeHandler,
                         Task\Priority\Handler $priorityHandler,
                         Handler               $commentHandler
    ): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);

        if (!$member = $memberFetcher->find($this->getUser()->getId())) {
            throw $this->createAccessDeniedException();
        }

        $statusCommand = Task\Status\Command::fromCommand($task);
        $statusForm = $this->createForm(Task\Status\Form::class, $statusCommand);
        $statusForm->handleRequest($request);
        if ($statusForm->isSubmitted() && $statusForm->isValid()) {
            try {
                $statusHandler->handle($statusCommand);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $progressCommand = Task\Progress\Command::fromCommand($task);
        $progressForm = $this->createForm(Task\Progress\Form::class, $progressCommand);
        $progressForm->handleRequest($request);
        if ($progressForm->isSubmitted() && $progressForm->isValid()) {
            try {
                $progressHandler->handle($progressCommand);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $typeCommand = Task\Type\Command::fromCommand($task);
        $typeForm = $this->createForm(Task\Type\Form::class, $typeCommand);
        $typeForm->handleRequest($request);
        if ($typeForm->isSubmitted() && $typeForm->isValid()) {
            try {
                $typeHandler->handle($typeCommand);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $priorityCommand = Task\Priority\Command::fromCommand($task);
        $priorityForm = $this->createForm(Task\Priority\Form::class, $priorityCommand);
        $priorityForm->handleRequest($request);
        if ($priorityForm->isSubmitted() && $priorityForm->isValid()) {
            try {
                $priorityHandler->handle($priorityCommand);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        $commentCommand = new Command(
            $this->getUser()->getId(),
            TaskEntity::class,
            (string)$task->getId()->getValue()
        );
        $commentForm = $this->createForm(CommentForm::class, $commentCommand);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            try {
                $commentHandler->handle($commentCommand);
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/show.html.twig', [
            'task' => $task,
            'project' => $task->getProject(),
            'comments' => $commentFetcher->allForTask($task->getId()->getValue()),
            'member' => $member,
            'priorityForm' => $priorityForm->createView(),
            'progressForm' => $progressForm->createView(),
            'statusForm' => $statusForm->createView(),
            'typeForm' => $typeForm->createView(),
            'children' => $taskFetcher->childrenOf($task->getId()->getValue()),
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route(path: '/{id}/files', name: '.files')]
    public function files(TaskEntity $task, Request $request, Task\Files\Add\Handler $handler, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        $command = new Task\Files\Add\Command($task->getId()->getValue(), $this->getUser()->getId());

        $form = $this->createForm(Task\Files\Add\Form::class, $command);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $files = [];

            foreach ($form->get('files')->getData() as $file) {

                $uploaded = $uploader->upload($file);
                $files[] = new File(
                    $uploaded->getPath(),
                    $uploaded->getName(),
                    $uploaded->getSize()
                );
            }

            $command->files = $files;

            try {
                $handler->handle($command);
                $this->addFlash('success', 'File was uploaded successfully');
                return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/files.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
            'project' => $task->getProject()
        ]);
    }

    #[Route(path: '/{task_id}/files/{file_id}', name: '.files.delete')]
    #[ParamConverter('task', options: ['id' => 'task_id'])]
    #[ParamConverter(FileEntity::class, options: ['id' => 'file_id'])]
    public function delete(TaskEntity $task, FileEntity $file, Request $request, Task\Files\Remove\Handler $handler, FileUploader $uploader): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::MANAGE, $task);

        if (!$this->isCsrfTokenValid('delete-file', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
        }

        $command = new Task\Files\Remove\Command($task->getId()->getValue(), $file->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'File was uploaded successfully');
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('work.projects.tasks.show', ['id' => $task->getId()]);
    }
}