<?php

namespace App\Controller;

use App\Entity\Task;
use App\Services\TaskService;
use App\Repository\TaskRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    public function __construct(private TaskService $taskService){}

    #[Route('/tasks', name: 'task_list')]
    public function listAction(TaskRepository $taskRepository)
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $task = new Task();
        $form = $this->createForm(\App\Form\TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $user = $this->getUser();

            if ($user === null){
                $this->addFlash('error', 'Impossible d\'ajouter la tache veuillez vous connecter.');
                return $this->redirectToRoute('app_login');                
            }

            $task->setCreatedAt(new \DateTimeImmutable);
            $task->setAuthor($user);
            $task->setIsDone(false);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_create');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task, ManagerRegistry $doctrine)
    {
        $task->setIsDone(!$task->isIsDone());
        $em = $doctrine->getManager();
        $em->flush();

        if ($task->isIsDone()){
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));
        } else {
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle()));
        }

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task)
    {
        $user = $this->getUser();

        $result = $this->taskService->deleteTask($task, $user);

        if (!$result){
            $this->addFlash('error', 'Vous devez être l\'auteur de la tâche pour la supprimer.');
        } else {
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        }

        return $this->redirectToRoute('task_list');
    }
}
