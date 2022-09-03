<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;

class TaskService
{

    public function __construct(private TaskRepository $repository){}

    public function getTask()
    {
        return $this->repository->findAll();
    }

    /**
     * Permet de crée une tâche
     * 
     * @return void
     */
    public function createTask(Task $task, User $userConnected): void
    {
        $task->setCreatedAt(new \DateTimeImmutable);
        $task->setAuthor($userConnected);
        $task->setIsDone(false);

        $this->repository->add($task, true);
    }

    /**
     * @return bool false si user n'est pas autorisé à supprimer la tâche
     */
    public function deleteTask(Task $task, User $userConnected): bool
    {
        if ($userConnected !== $task->getAuthor()){
            return false;
        }
        $this->repository->remove($task, true);
        return true;
    }

    /**
     * Permet de changer le statut d'une tâche en "terminée" ou "non faite"
     * 
     * @return bool Retourne l'état de la tâche ("terminée" ou "non faite") via un bool
     */
    public function toggleTask(Task $task): bool
    {
        $this->repository->toggle($task, true);
        return $task->isIsDone();
    }

    /**
     * Permet d'éditer une tâche
     * 
     * @return bool Retourne false si la tâche n'a pas pu être modifié
     */
    public function editTask(Task $task, User $userConnected)
    {
        if ($userConnected !== $task->getAuthor()){
            return false;
        }
        $this->repository->update();
        return true;
    }
}