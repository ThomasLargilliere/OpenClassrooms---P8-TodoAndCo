<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testListTask()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/tasks');

        // THEN
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForGetForm()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/tasks/create');

        // THEN
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForSubmitFormWhenUserIsNotConnected()
    {
        // GIVEN
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        // WHEN
        $this->client->submit($form);

        //THEN
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testCreateTaskForSubmitFormWhenUserIsConnected()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Admin');
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testToggleTaskWhenTaskIsDone()
    {
        // GIVEN
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setIsDone(true);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testToggleTaskWhenTaskIsNotDone()
    {
        // GIVEN
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setIsDone(false);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/toggle');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteTaskWhenUserIsNotConnected()
    {
        // GIVEN
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        
        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // THEN
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndAdminButTaskAuthorIsAno()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Admin');
        $this->client->loginUser($testUser);
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndAdminButTaskAuthorIsNotAno()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Admin');
        $thomas = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setAuthor($thomas);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndNotAdminButTaskAuthorIsNotHimself()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);

        $admin = $this->userRepository->findOneByUsername('Admin');
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setAuthor($admin);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndNotAdminButTaskAuthorIsUserIsConnected()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setAuthor($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');        
    }

    public function testEditTaskGetFormWhenUserIsNotConnected()
    {
        // GIVEN 
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        // THEN
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');  
    }

    public function testEditTaskGetFormWhenUserIsConnected()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');

        // THEN
        $this->assertResponseIsSuccessful();
    }

    public function testEditTaskSubmitFormWhenUserConnectedIsNotAuthor()
    {
        // GIVEN
        $admin = $this->userRepository->findOneByUsername('Admin');
        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setAuthor($admin);

        $testUser = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);
        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testEditTaskSubmitFormWhenUserConnectedIsAuthor()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByUsername('Thomas');
        $this->client->loginUser($testUser);

        $task = $this->taskRepository->findOneByTitle('Ma super tâche 0');
        $task->setAuthor($testUser);

        $crawler = $this->client->request('GET', '/tasks/' . $task->getId() . '/edit');
        $form = $crawler->selectButton('Modifier')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
    }
}