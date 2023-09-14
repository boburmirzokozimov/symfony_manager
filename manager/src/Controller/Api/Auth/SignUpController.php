<?php

namespace App\Controller\Api\Auth;

use App\Controller\ErrorHandler;
use App\Model\User\UseCase\SignUp;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SignUpController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator, private ErrorHandler $errorHandler)
    {
    }

    #[Route(path: '/auth/signup', name: '.auth.signup', methods: 'POST')]
    public function request(Request $request, SignUp\Request\Handler $handler): Response
    {
        $command = $this->serializer->deserialize($request->getContent(), SignUp\Request\Command::class, 'json');

        $violations = $this->validator->validate($command);
        if (count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return $this->json([], 200);
    }
}