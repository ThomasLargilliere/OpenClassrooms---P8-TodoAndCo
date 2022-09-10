<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testGetUserId()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);

        // WHEN
        $result = $user->getId();

        // THEN
        $this->assertIsInt($result);
    }

    public function testGetUserIdentifier()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);

        // WHEN
        $result = $user->getUserIdentifier();

        // THEN
        $this->assertIsString($result);
    }

    public function testGetEmail()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);

        // WHEN
        $result = $user->getEmail();

        // THEN
        $this->assertIsString($result);
    }

    public function testGetTasks()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);

        // WHEN
        $result = $user->getTasks();

        // THEN
        $this->assertIsObject($result);
    }

    public function testAddTask()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);
        $task = (new Task)->setTitle('Task Title')->setContent('Task Content');

        // WHEN
        $result = $user->addTask($task);

        // THEN
        $this->assertIsObject($result);
    }

    public function testRemoveTask()
    {
        // GIVEN
        $user = $this->userRepository->findOneById(1);
        $task = (new Task)->setTitle('Task Title')->setContent('Task Content');

        // WHEN
        $user->addTask($task);
        $result = $user->removeTask($task);

        // THEN
        $this->assertIsObject($result);
    }
}