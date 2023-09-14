<?php

namespace App\Controller\Auth;

use App\Model\User\UseCase\Reset\Request\Command;
use App\Model\User\UseCase\Reset\Request\Form;
use App\Model\User\UseCase\Reset\Request\Handler;
use App\Model\User\UseCase\Reset\Reset\Command as ResetCommand;
use App\Model\User\UseCase\Reset\Reset\Form as ResetForm;
use App\Model\User\UseCase\Reset\Reset\Handler as ResetHandler;
use App\ReadModel\User\UserFetcher;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route('api/v1/reset', name: 'auth.reset')]
	public function request(Request $request, Handler $handler): Response
	{
		$command = new Command();

		$form = $this->createForm(Form::class, $command);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Check your email');
				return $this->redirectToRoute('home');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/auth/reset/request.html.twig', ['form' => $form->createView()]);
	}

	#[Route('api/v1/reset/{token}', name: 'auth.reset.reset')]
	public function reset(string $token, Request $request, ResetHandler $handler, UserFetcher $fetcher): Response
	{
		if (!$fetcher->existByResetToken($token)) {
			$this->addFlash('error', 'Incorrect or already confirmed token');
			return $this->redirectToRoute('home');
		}

		$command = new ResetCommand($token);

		$form = $this->createForm(ResetForm::class, $command);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Password has been successfully changed');
				return $this->redirect('home');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}
		}

		return $this->render('app/auth/reset/reset.html.twig', ['form' => $form->createView()]);
	}
}