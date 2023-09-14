<?php

namespace App\Controller\Work\Projects\Project\Task;

use App\Controller\ErrorHandler;
use App\Model\Comment\Entity\Comment\Comment;
use App\Model\Comment\UseCase\Comment\Edit\Command;
use App\Model\Comment\UseCase\Comment\Edit\Form;
use App\Model\Comment\UseCase\Comment\Edit\Handler;
use App\Model\Comment\UseCase\Comment\Remove\Command as RemoveCommand;
use App\Model\Comment\UseCase\Comment\Remove\Handler as RemoveHandler;
use App\Model\Work\Entity\Projects\Task\Task;
use App\Security\Voter\CommentAccess;
use App\Security\Voter\TaskAccess;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/tasks', name: 'work.projects.tasks.comments')]
class CommentController extends AbstractController
{
    public function __construct(private ErrorHandler $errorHandler)
    {
    }

    #[Route(path: '/{task_id}/comments/{comment_id}/edit', name: '.edit')]
    #[ParamConverter(Task::class, options: ['id' => 'task_id'])]
    #[ParamConverter(Comment::class, options: ['id' => 'comment_id'])]
    public function edit(Task $task, Comment $comment, Request $request, Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);
        $this->checkCommentIsForTask($comment, $task);
        $this->denyAccessUnlessGranted(CommentAccess::MANAGE, $comment);

        $command = Command::fromCommand($task, $comment);

        $form = $this->createForm(Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Successfully updated comment.');
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    private function checkCommentIsForTask(Comment $comment, Task $task)
    {
        if (!($comment->getEntity()->getType() === Task::class && (int)$comment->getEntity()->getId() === $task->getId()->getValue())) {
            throw $this->createNotFoundException();
        }
    }

    #[Route(path: '/{task_id}/comments/{comment_id}/remove', name: '.remove')]
    #[ParamConverter('task', class: Task::class, options: ['mapping' => ['task_id' => 'id']])]
    #[ParamConverter('comment', class: Comment::class, options: ['mapping' => ['comment_id' => 'id', 'task_id' => 'entity_id']])]
    public function remove(Task $task, Comment $comment, Request $request, RemoveHandler $handler): Response
    {
        if (!$this->isCsrfTokenValid('remove-comment', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.tasks');
        }

        $this->denyAccessUnlessGranted(TaskAccess::VIEW, $task);
        $this->checkCommentIsForTask($comment, $task);
        $this->denyAccessUnlessGranted(CommentAccess::MANAGE, $comment);

        $command = new RemoveCommand($comment->getId()->getValue());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Successfully updated comment.');
        } catch (DomainException $e) {
            $this->errorHandler->handle($e);
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('work.projects.tasks.comments', ['id' => $task->getId()]);
    }
}