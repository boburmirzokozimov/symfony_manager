<?php

namespace App\Controller\Profile;

use App\Model\User\UseCase\Email\Confirm\Command as ConfirmCommand;
use App\Model\User\UseCase\Email\Confirm\Handler as ConfirmHandler;
use App\Model\User\UseCase\Email\Request\Command;
use App\Model\User\UseCase\Email\Request\Form;
use App\Model\User\UseCase\Email\Request\Handler;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: 'api/v1/profile/email')]
class EmailController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route(path: '', name: 'profile.email')]
	public function request(Request $request, Handler $handler): Response
	{
		$command = new Command($this->getUser()->getId());

		$form = $this->createForm(Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Check your Email');
				return $this->redirectToRoute('profile');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}

		}
		return $this->render('app/profile/email.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route(path: '/{token}', name: 'profile.email.confirm')]
	public function confirm(string $token, ConfirmHandler $handler): Response
	{
		$command = new ConfirmCommand($this->getUser()->getId(), $token);

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Email is successfully changed');
			return $this->redirectToRoute('profile');
		} catch (\DomainException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
			$this->addFlash('error', $e->getMessage());
			return $this->redirectToRoute('profile');
		}
	}
}