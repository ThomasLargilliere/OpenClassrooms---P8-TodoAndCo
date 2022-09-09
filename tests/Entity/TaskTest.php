<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testGetTaskId()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getId();

        // THEN
        $this->assertIsInt($result);
    }

    public function testGetTaskCreatedAt()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getCreatedAt();

        // THEN
        $this->assertIsObject($result);
    }

    public function testGetTaskTitle()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getTitle();

        // THEN
        $this->assertIsString($result);
    }

    public function testGetTaskContent()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getContent();

        // THEN
        $this->assertIsString($result);
    }
}