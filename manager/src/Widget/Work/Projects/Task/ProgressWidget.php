<?php

namespace App\Widget\Work\Projects\Task;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProgressWidget extends AbstractExtension
{
	public function getFunctions()
	{
		return [
			new TwigFunction('task_progress', [$this, 'progress'], ['needs_environment' => true, 'is_safe' => ['html']])
		];
	}

	public function progress(Environment $twig, int $progress)
	{
		return $twig->render('app/widget/work/projects/task/progress.html.twig', [
			'progress' => $progress,
		]);

	}
}