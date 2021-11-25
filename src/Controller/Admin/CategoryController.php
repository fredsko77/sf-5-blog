<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\Admin\CategoryType;
use App\Repository\CategoryRepository;
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
 * @Route("/admin/category")
 */
class CategoryController extends AbstractController
{

    /**
     * @var Filesystem $filesystem
     */
    private $filesystem;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var CategoryRepository $repository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager, Filesystem $filesystem, CategoryRepository $repository)
    {
        $this->manager = $manager;
        $this->filesystem = $filesystem;
        $this->repository = $repository;
    }

    /**
     * @Route("", name="admin_category_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->repository->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="admin_category_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    {
        $category = new Category;
        $slugger = new Slugify;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('uploadedFile')->getData();
            $category->setCreatedAt(new DateTime)
                ->setSlug($category->getSlug() ?? $slugger->slugify($category->getName()))
            ;

            if ($image instanceof UploadedFile) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('category_directory'),
                    $filename
                );

                $category->setImage('/uploads/categories/' . $filename);
            }

            $this->manager->persist($category);
            $this->manager->flush();

            return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/create.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $slugger = new Slugify;

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('uploadedFile')->getData();
            $category->setCreatedAt(new DateTime)
                ->setSlug($category->getSlug() ?? $slugger->slugify($category->getName()))
            ;

            if ($image instanceof UploadedFile) {
                $filename = md5(uniqid()) . '.' . $image->guessExtension();

                $image->move(
                    $this->getParameter('category_directory'),
                    $filename
                );

                $category->setImage('/uploads/categories/' . $filename);
            }

            $this->manager->flush();

            return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            if ($category->getImage() !== null) {
                if ($this->filesystem->exists($this->getParameter('kernel.project_dir') . '/public' . $category->getImage())) {
                    $this->filesystem->remove($this->getParameter('kernel.project_dir') . '/public' . $category->getImage());
                }
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
