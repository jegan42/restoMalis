<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/test')]
    public function index(): Response
    {
        return new Response('try in demo symfony 7 for resto 3306 pp');
    }
}
