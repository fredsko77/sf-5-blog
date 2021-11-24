<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/blog/comment", name="blog_comment")
     */
    public function index(): Response
    {
        return $this->render('blog/comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }
}
