<?php

namespace App\Controller\Work\Projects\Project;

use App\Model\Work\Entity\Projects\Role\Permission;
use App\Model\Work\Entity\Projects\Role\Role as RoleEntitty;
use App\Model\Work\UseCase\Projects\Role;
use App\ReadModel\Work\Projects\Role\RoleFetcher;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route(path: 'api/v1/work/projects/roles', name: 'work.projects.roles')]
class RoleController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route(path: '', name: '')]
	public function index(RoleFetcher $fetcher): Response
	{
		$roles = $fetcher->all();
		$permissions = Permission::names();

		return $this->render('app/work/projects/roles/index.html.twig', [
			'roles' => $roles,
			'permissions' => $permissions
		]);
	}

	#[Route(path: '/create', name: '.create')]
	public function create(Request $request, Role\Create\Handler $handler): Response
	{
		$command = new Role\Create\Command();

		$form = $this->createForm(Role\Create\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Role was successfully created.');
				return $this->redirectToRoute('work.projects.roles');
			} catch (DomainException $e) {
				$this->logger->error('Error', [$e->getMessage()]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/projects/roles/create.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route(path: '/{id}/role', name: '.role')]
	public function show(RoleEntitty $role): Response
	{
		return $this->render('app/work/projects/roles/show.html.twig', [
			'role' => $role
		]);
	}

	#[Route(path: '/{id}/edit', name: '.role.edit')]
	public function Edit(RoleEntitty $role, Request $request, Role\Edit\Handler $handler): Response
	{
		$command = Role\Edit\Command::fromCommand($role);

		$form = $this->createForm(Role\Edit\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Role was successfully edited.');
				return $this->redirectToRoute('work.projects.roles.role', ['id' => $role->getId()]);
			} catch (DomainException $e) {
				$this->logger->error('Error', [$e->getMessage()]);
				$this->addFlash('error', $e->getMessage());
			}
		}
		return $this->render('app/work/projects/roles/edit.html.twig', [
			'form' => $form->createView(),
			'role' => $role
		]);
	}

	#[Route(path: '/{id}/delete', name: '.role.delete')]
	public function delete(RoleEntitty $role, Request $request, Role\Remove\Handler $handler)
	{
		if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
			return $this->redirectToRoute('work.projects.roles');
		}

		$command = new Role\Remove\Command($role->getId());

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Role was successfully remover.');
			return $this->redirectToRoute('work.projects.roles');
		} catch (DomainException $e) {
			$this->logger->error('Error', [$e->getMessage()]);
			$this->addFlash('error', $e->getMessage());
		}
	}

	#[Route(path: '/{id}/copy', name: '.role.copy')]
	public function copy(RoleEntitty $role, Request $request, Role\Copy\Handler $handler): Response
	{
		$command = Role\Copy\Command::fromCommand($role);

		$form = $this->createForm(Role\Copy\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Role was successfully copied.');
				return $this->redirectToRoute('work.projects.roles');
			} catch (DomainException $e) {
				$this->logger->error('Error', [$e->getMessage()]);
				$this->addFlash('error', $e->getMessage());
			}
		}
		return $this->render('app/work/projects/roles/edit.html.twig', [
			'form' => $form->createView(),
			'role' => $role
		]);
	}
}