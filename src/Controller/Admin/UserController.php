<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\Admin\CreateUserType;
use App\Form\Admin\EditUserType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use App\Services\UserServicesInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/user", name="admin_user_")
 */
class UserController extends AbstractController
{

    /**
     * @var UserRepository $repository
     */
    private $repository;

    /**
     * @var PostRepository $postRepository
     */
    private $postRepository;

    /**
     * @var UserServicesInterface $service
     */
    private $service;

    /**
     * @var EntityManagerInterface $manager
     */

    public function __construct(UserRepository $repository, PostRepository $postRepository, UserServicesInterface $service, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->manager = $manager;
        $this->postRepository = $postRepository;
    }

    /**
     * @Route("", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->render('admin/user/index.html.twig', ['users' => $this->service->adminIndex($request)]);
    }

    /**
     * @Route("/create", name="create", methods={"GET","POST"})
     */
    public function create(Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = count($user->getRoles()) === 0 ? ['ROLE_USER'] : $user->getRoles();
            $user->setRegisteredAt(new DateTime)
                ->setPassword($hasher->hashPassword($user, 'password'))
                ->setConfirm(true)
                ->setRoles($roles)
                ->setIdentifier()
            ;

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('admin_user_edit', [
                'id' => $user->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/create.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_edit', [
                'id' => $user->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        $posts = $this->postRepository->findBy(['author' => $user], null, 5, 0);

        return $this->renderForm('admin/user/edit.html.twig', [
            'user' => $user,
            'posts' => $posts,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
