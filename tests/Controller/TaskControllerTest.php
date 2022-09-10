<?php

namespace App\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    public function testListTask()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForGetForm()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskForSubmitFormWhenUserIsNotConnected()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'Task Title',
            'task[content]' => 'Task Content'
        ]);

        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
}