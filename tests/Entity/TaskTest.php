<?php

namespace App\Tests\Service;

use App\Entity\Task;
use DateTimeImmutable;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testGetId()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getId();

        // THEN
        $this->assertIsInt($result);
    }

    public function testGetCreatedAt()
    {
        // GIVEN
        $task = $this->taskRepository->findOneById(1);

        // WHEN
        $result = $task->getCreatedAt();

        // THEN
        $this->assertIsObject($result);
    }
}