<?php

namespace App\Controller\Work\Projects\Project\Settings;

use App\Model\Work\Entity\Projects\Project\Project;
use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Form;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use App\Security\Voter\ProjectAccess;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route(path: 'api/v1/work/projects/{id}/calendar', name: 'work.projects.project.calendar')]
    public function show(Project $project, Request $request, CalendarFetcher $fetcher): Response
    {
        $this->denyAccessUnlessGranted(ProjectAccess::VIEW, $project);

        $now = new DateTimeImmutable();

        $query = Query::fromDate($now)->forMember($this->getUser()->getId());

        $form = $this->createForm(Form::class, $query);
        $form->handleRequest($request);

        $result = $fetcher->byMonth($query);

        return $this->render('app/work/projects/calendar.html.twig', [
            'form' => $form->createView(),
            'result' => $result,
            'now' => $now,
            'project' => $project,
            'dates' => iterator_to_array(new DatePeriod($result->getStart(), new DateInterval('P1D'), $result->getEnd())),
            'next' => $result->getMonth()->modify('+1 month'),
            'prev' => $result->getMonth()->modify('-1 month'),
        ]);
    }

}