<?php

namespace App\Controller;

use App\Annotation\Guid;
use App\Model\User\Entity\User\User;
use App\Model\User\UseCase\Active\Command as ActivateCommand;
use App\Model\User\UseCase\Active\Handler as ActivateHandler;
use App\Model\User\UseCase\Block\Command as BlockCommand;
use App\Model\User\UseCase\Block\Handler as BlockHandler;
use App\Model\User\UseCase\Create\Command;
use App\Model\User\UseCase\Create\Form;
use App\Model\User\UseCase\Create\Handler;
use App\Model\User\UseCase\Edit\Command as EditCommand;
use App\Model\User\UseCase\Edit\Form as EditForm;
use App\Model\User\UseCase\Edit\Handler as EditHandler;
use App\Model\User\UseCase\Role\Command as RoleCommand;
use App\Model\User\UseCase\Role\Form as RoleForm;
use App\Model\User\UseCase\Role\Handler as RoleHandler;
use App\Model\User\UseCase\SignUp\Confirm\Manual\Command as ConfirmCommand;
use App\Model\User\UseCase\SignUp\Confirm\Manual\Handler as ConfirmHandler;
use App\ReadModel\User\Filter\Filter;
use App\ReadModel\User\Filter\Form as FilterForm;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/users', name: 'users')]
#[IsGranted('ROLE_MANAGE_USERS')]
class    UsersController extends AbstractController
{
	private const PER_PAGE = 10;

	public function __construct(private UserFetcher $userFetcher, private LoggerInterface $logger)
	{
	}

	#[Route('', name: "")]
	public function index(Request $request): Response
	{
		$filter = new Filter();

		$form = $this->createForm(FilterForm::class, $filter);

		$form->handleRequest($request);

		$pagination = $this->userFetcher->all(
			$filter,
			$request->query->getInt('page', 1),
			self::PER_PAGE,
			$request->query->get('sort', 'date'),
			$request->query->get('direction', 'desc')
		);

		return $this->render('app/users/index.html.twig', [
			'form' => $form->createView(),
			'pagination' => $pagination
		]);
	}

	#[Route('/{id}', name: ".show", requirements: ['id' => Guid::PATTERN])]
	public function show(User $user): Response
	{
		return $this->render('app/users/show.html.twig', compact('user'));
	}

	#[Route('/{id}/edit', name: ".edit")]
	public function edit(User $user, Request $request, EditHandler $handler): Response
	{

		$command = EditCommand::fromUser($user);

		$form = $this->createForm(EditForm::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				return $this->redirectToRoute('users');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/users/edit.html.twig', [
			'form' => $form->createView(),
			'user' => $user
		]);
	}

	#[Route('/{id}/role', name: ".role")]
	public function role(User $user, Request $request, RoleHandler $handler): Response
	{
		if ($user->getId()->getValue() === $this->getUser()->getId()) {
			$this->addFlash('error', 'Unable to change a role for yourself.');
			return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
		}

		$command = RoleCommand::fromUser($user);

		$form = $this->createForm(RoleForm::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				return $this->redirectToRoute('users');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/users/role.html.twig', [
			'form' => $form->createView(),
			'user' => $user
		]);
	}

	#[Route('/create', name: ".create", priority: 1)]
	public function create(Request $request, Handler $handler): Response
	{
		$command = new Command();

		$form = $this->createForm(Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				return $this->redirectToRoute('users');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}

		}
		return $this->render('app/users/create.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route('/{id}/confirm', name: ".confirm", priority: 1)]
	public function confirm(User $user, Request $request, ConfirmHandler $handler): Response
	{
		if (!$this->isCsrfTokenValid('confirm', $request->request->get('token'))) {
			return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
		}

		$command = new ConfirmCommand($user->getId()->getValue());

		try {
			$handler->handle($command);
			return $this->redirectToRoute('users');
		} catch (\DomainException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
	}

	#[Route('/{id}/block', name: ".block", priority: 1)]
	public function block(User $user, Request $request, BlockHandler $handler): Response
	{
		if (!$this->isCsrfTokenValid('block', $request->request->get('token'))) {
			return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
		}

		$command = new BlockCommand($user->getId()->getValue());

		try {
			$handler->handle($command);
			return $this->redirectToRoute('users');
		} catch (\DomainException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
	}

	#[Route('/{id}/active', name: ".active", priority: 1)]
	public function active(User $user, Request $request, ActivateHandler $handler): Response
	{
		if (!$this->isCsrfTokenValid('active', $request->request->get('token'))) {
			return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
		}

		$command = new ActivateCommand($user->getId()->getValue());

		try {
			$handler->handle($command);
			return $this->redirectToRoute('users');
		} catch (\DomainException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('users.show', ['id' => $user->getId()]);
	}

}