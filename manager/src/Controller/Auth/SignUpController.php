<?php

namespace App\Controller\Auth;

use App\Model\User\UseCase\SignUp\Confirm\ByToken\Command as ConfirmCommand;
use App\Model\User\UseCase\SignUp\Confirm\ByToken\Handler as ConfirmHandler;
use App\Model\User\UseCase\SignUp\Request\Command;
use App\Model\User\UseCase\SignUp\Request\Form;
use App\Model\User\UseCase\SignUp\Request\Handler;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SignUpController extends AbstractController
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	#[Route('api/v1/signUp', name: 'auth.signup')]
	#[OA\RequestBody(content: new Model(type: Command::class))]
	public function request(Request $request, Handler $handler): Response
	{
		$command = new Command();

		$form = $this->createForm(Form::class, $command);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			try {
				$handler->handle($command);
				$this->addFlash('success', 'Check your Email');
				return $this->redirectToRoute('home');
			} catch (\DomainException $e) {
				$this->logger->error($e->getMessage(), ['exception' => $e]);
				$this->addFlash('error', $e->getMessage());
			}

		}
		return $this->render('app\auth\signup.html.twig', [
			'form' => $form->createView()
		]);
	}

	#[Route('api/v1/signUp/{token}', name: 'auth.signup.confirm')]
	public function confirm(string $token, ConfirmHandler $handler): Response
	{
		$command = new ConfirmCommand($token);

		try {
			$handler->handle($command);
			$this->addFlash('success', 'Email is successfully confirmed');
			return $this->redirectToRoute('home');
		} catch (\DomainException $e) {
			$this->logger->error($e->getMessage(), ['exception' => $e]);
			$this->addFlash('error', $e->getMessage());
			return $this->redirectToRoute('home');
		}
	}
}