<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project as ProjectEntity;
use App\Model\Work\UseCase\Projects\Project;
use App\Security\Voter\ProjectAccess;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/{id}/settings', name: 'work.projects.project.settings')]
class SettingsController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route(path: '', name: '', requirements: ['id' => Guid::PATTERN])]
	public function show(ProjectEntity $project): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

		return $this->render('app/work/projects/project/settings/show.html.twig', ['project' => $project]);
	}


	#[Route(path: '/edit', name: '.edit')]
	public function edit(ProjectEntity $project, Request $request, Project\Edit\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

		$command = Project\Edit\Command::fromProject($project);

		$form = $this->createForm(Project\Edit\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Group edited successfully');
				return $this->redirectToRoute('work.projects');
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/projects/project/settings/edit.html.twig', [
			'form' => $form->createView(),
			'project' => $project
		]);
	}

	#[Route(path: '/delete', name: '.delete')]
	public function delete(ProjectEntity $project, Request $request, Project\Remove\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			return $this->redirectToRoute('work.projects');
		}
		$command = new Project\Remove\Command($project->getId()->getValue());

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Group deleted successfully');
			return $this->redirectToRoute('work.projects');
		} catch (DomainException $e) {
			$this->logger->error($e->getMessage(), ['error' => $e->getMessage()]);
			$this->addFlash('error', $e->getMessage());
		}
	}

	#[Route(path: '/archive', name: '.archive')]
	public function archive(ProjectEntity $project, Request $request, Project\Archive\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

		if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
			return $this->redirectToRoute('work.projects');
		}
		$command = new Project\Archive\Command($project->getId()->getValue());

		$handler->handle($command);
		$this->addFlash('success', 'Group archived successfully');

		return $this->redirectToRoute('work.projects');
	}

	#[Route(path: '/reinstate', name: '.reinstate')]
	public function reinstate(ProjectEntity $project, Request $request, Project\Reinstate\Handler $handler): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::EDIT, $project);

		if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
			return $this->redirectToRoute('work.projects');
		}
		$command = new Project\Reinstate\Command($project->getId()->getValue());

		$handler->handle($command);
		$this->addFlash('success', 'Group activated successfully');

		return $this->redirectToRoute('work.projects');
	}
}