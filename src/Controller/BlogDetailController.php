<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogDetailController extends AbstractController
{
    #[Route('/blog/detail', name: 'app_blog_detail')]
    public function index(): Response
    {
        return $this->render('blog_detail/index.html.twig', [
            'controller_name' => 'BlogDetailController',
        ]);
    }
}
