<?php

namespace App\Controller\Admin;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Services\CommentServicesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/comment", name="admin_comment_")
 */
class CommentController extends AbstractController
{

    /**
     * @var CommentRepository $repository
     */
    private $repository;

    /**
     * @var CommentServicesInterface $service
     */
    private $service;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    public function __construct(CommentRepository $repository, CommentServicesInterface $service, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->manager = $manager;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments' => $this->service->adminComment($request),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"GET"})
     */
    public function delete(Comment $comment): Response
    {
        $this->manager->remove($comment);
        $this->manager->flush();

        return $this->redirectToRoute('admin_comment_index', [], Response::HTTP_SEE_OTHER);
    }
}
