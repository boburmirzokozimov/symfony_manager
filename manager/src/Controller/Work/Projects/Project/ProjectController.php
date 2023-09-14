<?php

namespace App\Controller\Work\Projects\Project;

use App\Annotation\Guid;
use App\Model\Work\Entity\Projects\Project\Project as ProjectEntity;
use App\Security\Voter\ProjectAccess;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/work/projects/{id}', name: 'work.projects.project')]
class ProjectController extends AbstractController
{
	#[Route(path: '', name: '', requirements: ['id' => Guid::PATTERN])]
	public function show(ProjectEntity $project): Response
	{
		$this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

		return $this->render('app/work/projects/project/settings/dashboard.html.twig', ['project' => $project]);
	}
}