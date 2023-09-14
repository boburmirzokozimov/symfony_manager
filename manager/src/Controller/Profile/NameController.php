<?php

namespace App\Controller\Profile;

use App\Model\User\UseCase\Name\Command;
use App\Model\User\UseCase\Name\Form;
use App\Model\User\UseCase\Name\Handler;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NameController extends AbstractController
{
	public function __construct(private LoggerInterface $logger, private UserFetcher $fetcher)
	{
	}

	#[Route(path: 'api/v1/profile/name', name: 'profile.name')]
	public function change(Request $request, Handler $handler)
	{
		$user = $this->fetcher->findDetail($this->getUser()->getId());

		$command = new Command($user->id);

		$form = $this->createForm(Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Your name was changed');
				return $this->redirectToRoute('profile');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}

		}
		return $this->render('app/profile/name.html.twig', [
			'form' => $form->createView()
		]);
	}
}