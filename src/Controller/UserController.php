<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserRepository $userRepository,)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }
    
    #[Route('/users', name: 'user_list')]
    public function list(): Response
    {
        $users = $this->em
            ->getRepository(User::class)
            ->findAll()
        ;

        return $this->render('user/list.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/users/create', name: 'user_create')]
    public function create(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this
            ->createForm(UserType::class, $user)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function edit(User $user, Request $request): Response
    {
        $form = $this
            ->createForm(UserType::class, $user)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form, 
            'user' => $user
        ]);
    }
}
