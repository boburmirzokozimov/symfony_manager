<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project as ProjectEntity;
use App\Model\Work\UseCase\Projects\Project\Department;
use App\ReadModel\Work\Projects\Project\Departments\DepartmentFetcher;
use App\Security\Voter\ProjectAccess;
use DomainException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/{project_id}/settings/departments', name: 'work.projects.project.settings.departments')]
#[ParamConverter('project', options: ['id' => 'project_id'])]
#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
class DepartmentController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route(path: '', name: '', requirements: ['id' => Guid::PATTERN])]
	public function index(ProjectEntity $project, DepartmentFetcher $fetcher): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

		return $this->render('app/work/projects/project/settings/departments/index.html.twig', [
			'project' => $project,
			'departments' => $fetcher->allOfProject($project->getId()->getValue())
		]);
	}

	#[Route(path: '/create', name: '.create', priority: 2)]
	public function create(ProjectEntity $project, Request $request, Department\Create\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

		$command = new Department\Create\Command($project->getId());

		$form = $this->createForm(Department\Create\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Group created successfully');
				return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/projects/project/settings/departments/create.html.twig', [
			'form' => $form->createView(),
			'project' => $project
		]);
	}

	#[Route(path: '/{department_id}/edit', name: '.edit')]
	public function edit(ProjectEntity $project, string $department_id, Request $request, Department\Edit\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

		$command = new Department\Edit\Command($project->getId()->getValue(), $department_id);

		$form = $this->createForm(Department\Edit\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Department edited successfully');
				return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/projects/project/settings/departments/edit.html.twig', [
			'form' => $form->createView(),
			'project' => $project
		]);
	}

	#[Route(path: '/{department_id}/delete', name: '.delete')]
	public function delete(ProjectEntity $project, string $department_id, Request $request, Department\Remove\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			return $this->redirectToRoute('work.projects.project.settings.departments');
		}
		$command = new Department\Remove\Command($project->getId()->getValue(), $department_id);

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Group deleted successfully');
			return $this->redirectToRoute('work.projects.project.settings.departments', ['project_id' => $project->getId()]);
		} catch (DomainException $e) {
			$this->logger->error($e->getMessage(), ['error' => $e->getMessage()]);
			$this->addFlash('error', $e->getMessage());
		}
	}

}