<?php

namespace App\Controller\Api\Profile;

use App\ReadModel\User\UserFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: '.user')]
    public function profile(UserFetcher $fetcher): Response
    {
        $user = $fetcher->findDetail($this->getUser()->getId());

        return $this->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }
}