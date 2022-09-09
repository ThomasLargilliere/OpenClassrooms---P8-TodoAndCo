<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\TaskService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskServiceTest extends KernelTestCase
{
    private TaskService $taskService;

    protected function setUp(): void
    {
        $this->taskService = static::getContainer()->get(TaskService::class);
    }

    public function testDeleteTaskWhenAuthorIsTheUserConnected()
    {
        $user = new User();
        $task = new Task();
        $task->setAuthor($user);

        $result = $this->taskService->deleteTask($task, $user);
        $this->assertTrue($result);
    }

    public function testDeleteTaskWhenTheUserConnectedIsNotAuthor()
    {
        $userConnected = new User();
        $task = new Task();
        $task->setAuthor(new User());

        $result = $this->taskService->deleteTask($task, $userConnected);
        $this->assertFalse($result);
    }

    public function testDeleteTaskWhenTheUserConnectedIsNotAuthorButIsAdmin()
    {
        // GIVEN
        $userConnected = new User('Test');
        $userConnected->setRoles(['ROLE_ADMIN']);

        $authorAno = (new User)->setUsername('anonyme');

        $task = new Task();
        $task->setAuthor($authorAno);

        // WHEN
        $result = $this->taskService->deleteTask($task, $userConnected);
        
        // THEN
        $this->assertTrue($result);
    }
}