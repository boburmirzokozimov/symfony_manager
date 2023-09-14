<?php

namespace App\Widget\Work\Projects;

use App\Model\User\Entity\User\User;
use App\ReadModel\Work\Projects\Calendar\CalendarFetcher;
use App\Security\UserIdentity;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalendarWidget extends AbstractExtension
{
    public function __construct(private TokenStorageInterface $tokenStorage, private CalendarFetcher $fetcher)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('work_projects_calendar', [$this, 'calendar'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    public function calendar(Environment $twig): string
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return '';
        }

        /** @var User $user */
        if (null === $user = $token->getUser() instanceof UserIdentity) {
            return '';
        }

        $now = new DateTimeImmutable();
        $result = $this->fetcher->byWeek($now, $token->getUser()->getId());

        return $twig->render('app/widget/calendar.html.twig', [
            'dates' => iterator_to_array(new DatePeriod($result->start, new DateInterval('P1D'), $result->end)),
            'now' => $now,
            'result' => $result
        ]);

    }

}