<?php

namespace App\Controller;

use App\Form\Blog\SearchPostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var PostRepository $postRepository
     */
    private $postRepository;

    /**
     * @var CategoryRepository $categoryRepository
     */
    private $categoryRepository;

    public function __construct(PostRepository $postRepository, CategoryRepository $categoryRepository)
    {
        $this->postRepository = $postRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/home", name="app_home", methods={"GET"})
     * @Route("/", name="app_default", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        $posts = $this->postRepository->latest();
        $categories = $this->categoryRepository->findAll();
        $form = $this->createForm(SearchPostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $query = $form->query->get('query');
        }

        return $this->renderForm('home/index.html.twig', [
            'posts' => $posts,
            'categories' => $categories,
            'form' => $form,
        ]);
    }
}
