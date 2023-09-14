<?php

namespace App\Widget\Work\Projects\Task;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PriorityWidget extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('task_priority', [$this, 'priority'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function priority(Environment $twig, int $priority)
    {
        return $twig->render('app/widget/work/projects/task/priority.html.twig', [
            'priority' => $priority,
        ]);

    }
}