<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Route('/users', name: 'admin_users_')]
class UserController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * List all users
     *
     * @return Response
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): Response
    {
        $users = $this->em
            ->getRepository(User::class)
            ->findAll()
        ;

        return $this->render('admin/user/list.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Create a user
     *
     * @param Request $request
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @return Response
     */
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this
            ->createForm(UserType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit a user
     * 
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(User $user, Request $request): Response
    {
        $form = $this
            ->createForm(UserType::class, $user)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('admin_users_list');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
