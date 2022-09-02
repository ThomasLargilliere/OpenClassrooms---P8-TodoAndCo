<?php
declare(strict_types=1);

namespace App\Services;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;

class TaskService
{

    public function __construct(private TaskRepository $repository){}
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
}