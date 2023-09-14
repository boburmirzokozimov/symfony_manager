<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('api/v1/home', name: 'home',methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Home page',
    )]
    public function action(): Response
    {
        return $this->render('app\home.html.twig');
    }
}