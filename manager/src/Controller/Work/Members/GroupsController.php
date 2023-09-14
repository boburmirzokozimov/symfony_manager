<?php

namespace App\Controller\Work\Members;

use App\Model\Work\Entity\Members\Group\Group as EntityGroup;
use App\Model\Work\UseCase\Members\Group;
use App\ReadModel\Work\Members\Groups\GroupFetcher;
use Doctrine\DBAL\Exception;
use DomainException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/members/groups', name: 'work.members.groups')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
class GroupsController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	/**
	 * @throws Exception
	 */
	#[Route(path: '', name: '')]
	public function index(GroupFetcher $fetcher): Response
	{
		$groups = $fetcher->all();

		return $this->render('app/work/members/groups/index.html.twig', compact('groups'));
	}

	#[Route(path: '/create', name: '.create', priority: 1)]
	public function create(Request $request, Group\Create\Handler $handler): Response
	{
		$command = new Group\Create\Command();

		$form = $this->createForm(Group\Create\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Group created successfully');
				return $this->redirectToRoute('work.members.groups');
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/members/groups/create.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route(path: '/{id}/edit', name: '.edit')]
	public function edit(EntityGroup $group, Request $request, Group\Edit\Handler $handler): Response
	{
		$command = new Group\Edit\Command($group->getId()->getValue());

		$form = $this->createForm(Group\Edit\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Group edited successfully');
				return $this->redirectToRoute('work.members.groups');
			} catch (DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/members/groups/edit.html.twig', [
			'form' => $form->createView(),
			'group' => $group
		]);
	}

	#[Route(path: '/{id}/delete', name: '.delete')]
	public function delete(EntityGroup $group, Request $request, Group\Remove\Handler $handler): Response
	{
		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			return $this->redirectToRoute('work.members.groups.show', ['id' => $group->getId()]);
		}
		$command = new Group\Remove\Command($group->getId()->getValue());

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Group deleted successfully');
		} catch (DomainException $e) {
			$this->logger->error($e->getMessage(), ['error' => $e->getMessage()]);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('work.members.groups');
	}
}