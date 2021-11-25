<?php

namespace App\Controller\Blog;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/blog", name="blog_post_")
 */
class PostController extends AbstractController
{

    /**
     * @var PostRepository $repository
     */
    private $repository;

    public function __construct(PostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route(
     *  "/{id}-{slug}",
     *  name="show",
     *  requirements={"id": "\d+", "slug": "[a-z0-9\-]+"},
     *  methods={"GET"}
     * )
     */
    public function show(int $id, string $slug): Response
    {
        $post = $this->repository->find($id);

        // TODO: gestion des erreurs 404
        // TODO: voter pour vérifier si l'utilisateur a le droit d'accéder à cet article

        if ($post->getSlug() !== $slug) {
            return $this->redirectToRoute('blog_post_show', [
                'id' => $post->getId(),
                'slug' => $post->getSlug(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/post/show.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }

}
