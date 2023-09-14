<?php

namespace App\Controller\Work\Projects\Project;

use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\ReadModel\Work\Projects\Calendar\Query\Form;
use App\ReadModel\Work\Projects\Calendar\Query\Query;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    #[Route(path: 'api/v1/work/projects/calendar', name: 'work.projects.calendar')]
    public function show(Request $request, CalendarFetcher $fetcher): Response
    {
        $now = new DateTimeImmutable();

        if ($this->isGranted('ROLE_WORK_MANAGE_PROJECTS')) {
            $query = Query::fromDate($now);
        } else {
            $query = Query::fromDate($now)->forMember($this->getUser()->getId());
        }

        $form = $this->createForm(Form::class, $query);
        $form->handleRequest($request);

        $result = $fetcher->byMonth($query);

        return $this->render('app/work/projects/calendar.html.twig', [
            'form' => $form->createView(),
            'result' => $result,
            'now' => $now,
            'project' => null,
            'dates' => iterator_to_array(new DatePeriod($result->getStart(), new DateInterval('P1D'), $result->getEnd())),
            'next' => $result->getMonth()->modify('+1 month'),
            'prev' => $result->getMonth()->modify('-1 month'),
        ]);
    }

}