<?php

namespace App\Controller\Api\Profile;

use App\Model\User\UseCase\Edit\Command;
use App\Model\User\UseCase\Name\Handler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NameController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer, private ValidatorInterface $validator)
    {
    }

    #[Route(path: '/profile/name', name: 'profile.name', methods: ['PUT'])]
    public function name(Request $request, Handler $handler): Response
    {
        $command = $this->serializer->deserialize($request->getContent(), Command::class, 'json', [
            'object_to_populate' => new Command($this->getUser()->getId()),
            'ignored_attributes' => ['id'],
        ]);

        $violations = $this->validator->validate($command);
        if (count($violations)) {
            $json = $this->serializer->serialize($violations, 'json');
            return new JsonResponse($json, Response::HTTP_BAD_REQUEST, [], true);
        }

        $handler->handle($command);

        return $this->json([], 200);
    }
}