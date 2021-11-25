<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/register", name="register")
     */
    public function index(UserPasswordHasherInterface $hasher, Request $request): Response
    {
        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRegisteredAt(new DateTime)
                ->setPassword($hasher->hashPassword($user, $user->getPassword()))
                ->setRoles(['ROLE_USER'])
                ->setConfirm(true)
            ;

            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auth/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
