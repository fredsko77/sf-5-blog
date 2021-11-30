<?php
namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog", name="blog_")
 */
class DefaultController extends AbstractController
{

    /**
     * @Route(
     *  "",
     *  name="index",
     *  methods={"GET"}
     * )
     */
    public function index(Request $request): Response
    {
        return $this->renderForm('blog/index.html.twig', []);
    }

}
