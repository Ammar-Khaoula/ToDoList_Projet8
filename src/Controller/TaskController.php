<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        return $this->render(
            'task/list.html.twig',
            [
                'tasks' => $taskRepository->findAll(),
                'users' => $userRepository->findAll()
            ]
        );
    }
    
    #[Route('/tasksToDo', name: 'tasks_to_do')]
    public function tasksToDo(TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findBy(['isDone' => false]),
            'users' => $userRepository->findAll()
        ]);
    } 

    #[Route('/tasksCompleted', name: 'tasks_completed')]
    public function tasksCompleted(TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findBy(['isDone' => true]),
            'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $task = new Task();

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() and  $form->isValid()) {

            $task->setUser($this->getUser());
            $task->toggle(false);
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editAction(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {

            //$em->persist($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction(Task $task, EntityManagerInterface $em): Response
    {
        $task->toggle(!$task->isDone());
        $em->persist($task);
        $em->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTaskAction(Task $task, EntityManagerInterface $em): Response
    {
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
