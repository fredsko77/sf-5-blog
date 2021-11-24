<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\PostServicesInterface;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/post")
 */
class PostController extends AbstractController
{

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var PostRepository $repository
     */
    private $repository;

    /**
     * @var PostServicesInterface $service
     */
    private $service;

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    /**
     * @var Slugify $slugger
     */
    private $slugger;

    public function __construct(EntityManagerInterface $manager, PostRepository $repository, PostServicesInterface $service)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->service = $service;
        $this->filesystem = new Filesystem;
        $this->slugger = new Slugify;
    }

    private function getPostDir()
    {
        return $this->getParameter('post_directory');
    }

    private function getRootDir()
    {
        return $this->getParameter('root_directory');
    }

    /**
     * @Route("", name="admin_post_index", methods={"GET"})
     */
    public function index(Request $request): Response
    {

        return $this->render('admin/post/index.html.twig', ['posts' => $this->service->adminPost($request)]);
    }

    /**
     * @Route("/create", name="admin_post_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('uploadedFile')->getData();
            $post->setUpdatedAt(new DateTime)
                ->setSlug($post->getSlug() ?? $this->slugger->slugify($post->getTitle()))
            ;

            if ($image instanceof UploadedFile) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getPostDir(),
                    $filename
                );

                $post->setImage('/uploads/posts/' . $filename);
            }

            $this->manager->persist($post);
            $this->manager->flush();

            return $this->redirectToRoute('admin_post_edit', [
                'id' => $post->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/post/action.html.twig', [
            'post' => $post,
            'form' => $form,
            'action' => 'create',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_post_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('uploadedFile')->getData();
            $post->setUpdatedAt(new DateTime)
                ->setSlug($post->getSlug() ?? $this->slugger->slugify($post->getTitle()))
            ;

            $currentImage = $post->getImage();

            if ($image instanceof UploadedFile) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getPostDir(),
                    $filename
                );

                if ($currentImage !== null) {
                    if ($this->filesystem->exists($this->getRootDir() . $currentImage)) {
                        $this->filesystem->remove($this->getRootDir() . $currentImage);
                    }
                }

                $post->setImage('/uploads/posts/' . $filename);
            }

            $this->manager->flush();

            return $this->redirectToRoute('admin_post_edit', [
                'id' => $post->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/post/action.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
            'action' => 'edit',
        ]);
    }

    /**
     * @Route("/{id}", name="admin_post_delete", methods={"POST"})
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
            $this->manager->remove($post);
            $this->manager->flush();
        }

        return $this->redirectToRoute('admin_post_index', [], Response::HTTP_SEE_OTHER);
    }
}
