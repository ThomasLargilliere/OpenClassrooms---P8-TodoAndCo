<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private UserRepository $userRepository;
    private TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testListUserWhenUserIsNotConnected()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/users');

        // THEN
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testListUserWhenUserIsConnectedButNotAdmin()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('user0@user.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/users');

        // THEN
        $this->assertResponseRedirects('/tasks');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testListUserWhenUserIsConnectedAndIsAdmin()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('admin@admin.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/users');

        // THEN
        $this->assertResponseIsSuccessful();
    }
}