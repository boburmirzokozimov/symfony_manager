<?php

namespace App\Controller\Work\Members;

use App\Annotation\Guid;
use App\Controller\ErrorHandler;
use App\Model\User\Entity\User\User;
use App\Model\Work\Entity\Members\Member\Member as MemberEntity;
use App\Model\Work\UseCase\Members\Member;
use App\ReadModel\Work\Members\Member\MemberFetcher;
use App\ReadModel\Work\Projects\Project\Departments\DepartmentFetcher;
use DomainException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/members', name: 'work.members.member')]
#[IsGranted('ROLE_WORK_MANAGE_MEMBERS')]
class MembersController extends AbstractController
{
	public function __construct(private ErrorHandler $error)
	{
	}


	#[Route(path: '', name: '')]
	public function index(MemberFetcher $fetcher): Response
	{
		$members = $fetcher->all();

		return $this->render('app\work\members\member\index.html.twig', [
			'members' => $members
		]);
	}

	#[Route(path: '/{id}', name: '.show', requirements: ['id' => Guid::PATTERN])]
	public function show(MemberEntity $member, DepartmentFetcher $fetcher): Response
	{
		$departments = $fetcher->allOfMember($member->getId());

		return $this->render('app/work/members/member/show.html.twig', ['member' => $member, 'departments' => $departments]);
	}

	#[Route(path: '/{id}/edit', name: '.edit')]
	public function edit(MemberEntity $member, Request $request, Member\Edit\Handler $handler): Response
	{
		$command = Member\Edit\Command::fromMember($member);

		$form = $this->createForm(Member\Edit\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Edited successfully');
				return $this->redirectToRoute('work.members.member');
			} catch (DomainException $e) {
				$this->error->handle($e);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/members/member/edit.html.twig', [
			'form' => $form->createView(),
			'member' => $member,
		]);
	}

	#[Route(path: '/create/{id}', name: '.create')]
	public function create(User $user, Request $request, Member\Create\Handler $handler): Response
	{
		$command = Member\Create\Command::fromMember($user);

		$form = $this->createForm(Member\Create\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Member was successfully created.');
			} catch (DomainException $e) {
				$this->error->handle($e);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/members/member/create.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route(path: '/{id}/move', name: '.move')]
	public function move(MemberEntity $member, Request $request, Member\Move\Handler $handler): Response
	{
		$command = Member\Move\Command::fromMember($member);

		$form = $this->createForm(Member\Move\Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Member was successfully moved.');
			} catch (DomainException $e) {
				$this->error->handle($e);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/work/members/member/move.html.twig', [
			'form' => $form->createView(),
			'member' => $member,
		]);
	}

	#[Route(path: '/{id}/archive', name: '.archive')]
	public function archive(MemberEntity $member, Request $request, Member\Archive\Handler $handler): Response
	{
		if (!$this->isCsrfTokenValid('archive', $request->request->get('token'))) {
			return $this->redirectToRoute('work.members.member', ['id' => $member->getId()]);
		}
		$command = new Member\Archive\Command($member->getId());

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Member was successfully archived.');
		} catch (DomainException $e) {
			$this->error->handle($e);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('work.members.member.show', ['id' => $member->getId()]);
	}

	#[Route(path: '/{id}/reinstate', name: '.archive')]
	public function reinstate(MemberEntity $member, Request $request, Member\Reinstate\Handler $handler): Response
	{
		if (!$this->isCsrfTokenValid('reinstate', $request->request->get('token'))) {
			return $this->redirectToRoute('work.members.member', ['id' => $member->getId()]);
		}
		$command = new Member\Reinstate\Command($member->getId());

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Member was successfully archived.');
		} catch (DomainException $e) {
			$this->error->handle($e);
			$this->addFlash('error', $e->getMessage());
		}
		return $this->redirectToRoute('work.members.member.show', ['id' => $member->getId()]);
	}
}