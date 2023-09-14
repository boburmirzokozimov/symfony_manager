<?php

namespace App\Controller\Work\Projects\Project\Task;

use App\Controller\ErrorHandler;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Task as UseCase;
use App\ReadModel\Work\Projects\Task\Filter\Filter;
use App\ReadModel\Work\Projects\Task\Filter\Form;
use App\ReadModel\Work\Projects\Task\TaskFetcher;
use App\Security\Voter\ProjectAccess;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/{project_id}/tasks', name: 'work.projects.project.tasks')]
#[ParamConverter('project', options: ['id' => 'project_id'])]
#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
class TasksController extends AbstractController
{
    public const PER_PAGE = 50;

    public function __construct(private ErrorHandler $errorHandler)
    {
    }

    #[Route(path: '', name: '')]
    public function index(Project $project, TaskFetcher $fetcher, Request $request)
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $filter = Filter::forProject($project->getId()->getValue());

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
            'pagination' => $pagination,
            'project' => $project,
            'form' => $form->createView(),
        ]);

    }

    #[Route(path: '/create', name: '.create')]
    public function create(Project $project, Request $request, UseCase\Create\Handler $handler): Response
    {
        $command = new UseCase\Create\Command(
            $project->getId()->getValue(),
            $this->getUser()->getId()
        );

        if ($parent = $request->query->get('parent')) {
            $command->parent = $parent;
        }

        $form = $this->createForm(UseCase\Create\Form::class, $command);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                return $this->redirectToRoute('work.projects.project.tasks', ['project_id' => $project->getId()]);
            } catch (DomainException $e) {
                $this->errorHandler->handle($e);
                $this->addFlash('error', $e->getMessage());
            }
        }

        return $this->render('app/work/projects/task/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project
        ]);
    }
}