<?php

namespace App\Controller\Api;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route(path: '/home', name: 'home')]
    public function home(): Response
    {
        return $this->json([
            'name' => 'JSON API',
        ]);
    }

    #[Route(path: '/profile', name: '.user')]
    public function show(UserFetcher $fetcher): Response
    {
        $user = $fetcher->findDetail($this->getUser()->getId());

        return $this->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}