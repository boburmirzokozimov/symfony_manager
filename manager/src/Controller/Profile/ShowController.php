<?php

namespace App\Controller\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends AbstractController
{
	public function __construct(private UserFetcher $fetcher)
	{
	}

	#[Route(path: 'api/v1/profile', name: 'profile')]
	public function index(): Response
	{
		$user = $this->fetcher->findDetail($this->getUser()->getId());

		return $this->render('app/profile/show.html.twig', compact('user'));
	}
}
