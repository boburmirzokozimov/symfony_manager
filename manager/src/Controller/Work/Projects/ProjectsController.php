<?php

namespace App\Controller\Work\Projects;

use App\Model\Work\UseCase\Projects\Project;
use App\ReadModel\Work\Projects\Project\Filter\Filter;
use App\ReadModel\Work\Projects\Project\Filter\Form;
use App\ReadModel\Work\Projects\Project\ProjectFetcher;
use DomainException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects', name: 'work.projects')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
class ProjectsController extends AbstractController
{
	private const PER_PAGE = 10;

	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route(path: '', name: '')]
	public function index(ProjectFetcher $fetcher, Request $request): Response
	{
		if ($this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
			$filter = Filter::all();
		} else {
			$filter = Filter::forMember($this->getUser()->getId());
		}

		$form = $this->createForm(Form::class, $filter);
		$form->handleRequest($request);

		$pagination = $fetcher->all(
			$filter,
			$request->query->getInt('page', 1),
			self::PER_PAGE,
			$request->query->get('sort', 'name'),
			$request->query->get('direction', 'desc')
		);

		return $this->render('app/work/projects/project/index.html.twig', [
			'pagination' => $pagination,
			'form' => $form->createView()
		]);
	}


	#[Route(path: '/create', name: '.create', priority: 2)]
	public function create(Request $request, ProjectFetcher $projectFetcher, Project\Create\Handler $handler): Response
	{
		$command = new Project\Create\Command();
		$command->sort = $projectFetcher->getMaxSort() + 1;

		$form = $this->createForm(Project\Create\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Group created successfully');
				return $this->redirectToRoute('work.projects');
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/projects/project/create.html.twig', [
			'form' => $form->createView(),
		]);
	}


}