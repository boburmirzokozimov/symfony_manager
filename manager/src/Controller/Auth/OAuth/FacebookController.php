<?php

namespace App\Controller\Auth\OAuth;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FacebookController extends AbstractController
{
	#[Route(path: 'api/v1/connect/facebook', name: "connect_facebook_start")]
	public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
	{
		return $clientRegistry
			->getClient('facebook_main')
			->redirect(['public_profile']);
	}

	#[Route(path: 'api/v1/connect/facebook/check', name: "connect_facebook_check")]
	public function connectCheckAction(Request $request, ClientRegistry $clientRegistry): RedirectResponse
	{
		dd($clientRegistry);
		return $this->redirectToRoute('home');
	}
}