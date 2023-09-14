<?php

namespace App\Widget\Work\Projects\Project;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectWidget extends AbstractExtension
{
	public function getFunctions()
	{
		return [
			new TwigFunction('project_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' => ['html']])
		];
	}

	public function status(Environment $twig, string $status)
	{
		return $twig->render('app/widget/work/projects/project/status.html.twig', [
			'status' => $status,
		]);

	}
}