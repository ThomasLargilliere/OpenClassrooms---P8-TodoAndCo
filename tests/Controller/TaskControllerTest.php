<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }


    public function testListTask()
    {
        $crawler = $this->client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForGetForm()
    {
        $crawler = $this->client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForSubmitFormWhenUserIsNotConnected()
    {
        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testCreateTaskForSubmitFormWhenUserIsConnected()
    {
        $testUser = $this->userRepository->findOneByEmail('admin@admin.fr');
        $this->client->loginUser($testUser);

        $crawler = $this->client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}