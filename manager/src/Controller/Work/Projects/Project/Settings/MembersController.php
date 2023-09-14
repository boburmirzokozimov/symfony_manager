<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Model\Work\Entity\Projects\Project\Membership as MembershipEntity;
use App\Model\Work\Entity\Projects\Project\Project;
use App\Model\Work\UseCase\Projects\Project\Membership;
use App\Security\Voter\ProjectAccess;
use DomainException;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/{project_id}/settings/members', name: 'work.projects.project.settings.membership')]
#[ParamConverter('project', options: ['id' => 'project_id'])]
#[IsGranted('ROLE_WORK_MANAGE_PROJECTS')]
class MembersController extends AbstractController
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[Route(path: '', name: '')]
    public function index(Project $project): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        return $this->render('app/work/projects/project/settings/members/index.html.twig', [
            'project' => $project,
            'memberships' => $project->getMemberships()
        ]);
    }

    #[Route(path: '/assign', name: '.assign')]
    public function assign(Project $project, Request $request, Membership\Add\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $command = new Membership\Add\Command($project->getId());

        $form = $this->createForm(Membership\Add\Form::class, $command, ['project' => $project->getId()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully assigned');
                return $this->redirectToRoute('work.projects.project.settings.membership', ['project_id' => $project->getId()]);
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                $this->logger->error($e->getMessage(), [$e->getTrace()]);
            }
        }

        return $this->render('app/work/projects/project/settings/members/create.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route(path: '/{membership}/edit', name: '.edit')]
    public function edit(Project $project, MembershipEntity $membership, Request $request, Membership\Edit\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);

        $command = Membership\Edit\Command::fromCommand($project, $membership);

        $form = $this->createForm(Membership\Edit\Form::class, $command, ['project' => $project->getId()]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $handler->handle($command);
                $this->addFlash('success', 'Member was successfully assigned');
                return $this->redirectToRoute('work.projects.project.settings.membership', ['project_id' => $project->getId()]);
            } catch (DomainException $e) {
                $this->addFlash('error', $e->getMessage());
                $this->logger->error($e->getMessage(), [$e->getTrace()]);
            }
        }

        return $this->render('app/work/projects/project/settings/members/edit.html.twig', [
            'form' => $form->createView(),
            'project' => $project,
        ]);
    }

    #[Route(path: '/{membership}/revoke', name: '.revoke')]
    public function revoke(Project $project, MembershipEntity $membership, Request $request, Membership\Remove\Handler $handler): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::MANAGE_MEMBERS, $project);


        if (!$this->isCsrfTokenValid('revoke', $request->request->get('token'))) {
            return $this->redirectToRoute('work.projects.project.settings.membership', ['project_id' => $project->getId()]);
        }

        $command = new Membership\Remove\Command($project->getId(), $membership->getMember()->getId());

        try {
            $handler->handle($command);
            $this->addFlash('success', 'Member was successfully assigned');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
            $this->logger->error($e->getMessage(), [$e->getTrace()]);
        }
        return $this->redirectToRoute('work.projects.project.settings.membership', ['project_id' => $project->getId()]);
    }
}