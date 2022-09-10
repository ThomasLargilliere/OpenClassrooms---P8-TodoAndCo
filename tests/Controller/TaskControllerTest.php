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
        $testUser = $this->userRepository->findOneByEmail('admin@admin.fr');
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
        // WHEN
        $crawler = $this->client->request('GET', '/tasks/1/toggle');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testToggleTaskWhenTaskIsNotDone()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/tasks/3/toggle');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteTaskWhenUserIsNotConnected()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/tasks/2/delete');

        // THEN
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndAdminButTaskAuthorIsAno()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('admin@admin.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/2/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndAdminButTaskAuthorIsNotAno()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('admin@admin.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/16/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndNotAdminButTaskAuthorIsNotHimself()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('user0@user.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/14/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');        
    }

    public function testDeleteTaskWhenUserIsConnectedAndNotAdminButTaskAuthorIsUserIsConnected()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('user0@user.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/tasks/13/delete');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');        
    }
}