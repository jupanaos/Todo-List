<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class TaskController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    #[Route('/tasks', name: 'task_list', methods: ['GET'])]
    public function list(): Response
    {
        $tasks = $this->em
        ->getRepository(Task::class)
        ->findAll();

        return $this->render('app/pages/task/list.html.twig', [
            'tasks' => $tasks
        ]);
    }

    #[Route('/tasks/create', name: 'task_create', methods: ['GET', 'POST'])]
    public function create(Request $request, TaskRepository $taskRepository): Response
    {
        $task = new Task();
        $form = $this
            ->createForm(TaskType::class, $task)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setAuthor($this->getUser());

            $taskRepository->add($task, true);

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('app/pages/task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit', methods: ['GET', 'POST'])]
    #[IsGranted('TASK_EDIT', subject: 'task', message: 'Vous n\'avez pas les droits pour éditer cette tâche!')]
    public function edit(Task $task, Request $request, TaskRepository $taskRepository): Response
    {
        $form = $this
            ->createForm(TaskType::class, $task)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            $taskRepository->add($task, true);

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('app/pages/task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task): Response
    {
        $task->toggle(!$task->isDone());
        $this->em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    #[IsGranted('TASK_DELETE', subject: 'task', message: 'Vous n\'avez pas les droits pour supprimer cette tâche!')]
    public function deleteTask(Task $task, TaskRepository $taskRepository)
    {
        $taskRepository->remove($task, true);

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
