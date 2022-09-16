<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{

    public function __construct(private TaskService $taskService){}

    #[Route('/tasks', name: 'task_list')]
    public function listTask(): Response
    {
        $tasks = $this->taskService->getTask();
        return $this->render('task/list.html.twig', ['tasks' => $tasks]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createTask(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(\App\Form\TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if ($user === null){
                $this->addFlash('error', 'Impossible d\'ajouter la tache veuillez vous connecter.');
                return $this->redirectToRoute('app_login');                
            }

            $this->taskService->createTask($task, $user);
            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle')]
    public function toggleTask(Task $task): RedirectResponse
    {
        $result = $this->taskService->toggleTask($task);

        $typeFlash = 'success';
        $messageFlash = sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle());

        if (!$task->isIsDone()){
            $typeFlash = 'error';
            $messageFlash = sprintf('La tâche %s a bien été marquée comme non terminée.', $task->getTitle());
        }

        $this->addFlash($typeFlash, $messageFlash);
        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/delete', name: 'task_delete')]
    public function deleteTask(Task $task): RedirectResponse
    {
        $user = $this->getUser();
        if ($user === null){
            $this->addFlash('error', 'Impossible de supprimer la tache veuillez vous connecter.');
            return $this->redirectToRoute('app_login');                
        }

        $result = $this->taskService->deleteTask($task, $user);

        $typeFlash = 'success';
        $messageFlash = 'La tâche a bien été supprimée.';

        if (!$result){
            $typeFlash = 'error';
            $messageFlash = 'Vous devez être l\'auteur de la tâche pour la supprimer.';
        }

        $this->addFlash($typeFlash, $messageFlash);

        return $this->redirectToRoute('task_list');
    }

    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function editTask(Request $request, Task $task): Response
    {
        $user = $this->getUser();

        if ($user === null){
            $this->addFlash('error', 'Vous devez être connecté pour modifier une tâche');
            return $this->redirectToRoute('app_login');                
        }

        $form = $this->createForm(\App\Form\TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $result = $this->taskService->editTask($task, $user);

            $typeFlash = 'success';
            $messageFlash = 'La tâche a bien été modifiée.';

            if (!$result){
                $typeFlash = 'error';
                $messageFlash = 'Vous devez être l\'auteur de cette tâche pour la modifier.';                
            }

            $this->addFlash($typeFlash, $messageFlash);
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

}
