<?php

namespace App\Tests\Service;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testLoginWhenUserIsNotConnected()
    {
        // WHEN
        $crawler = $this->client->request('GET', '/login');

        // THEN
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWhenUserIsConnected()
    {
        // GIVEN
        $testUser = $this->userRepository->findOneByEmail('user0@user.fr');
        $this->client->loginUser($testUser);

        // WHEN
        $crawler = $this->client->request('GET', '/login');

        // THEN
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWhenUserIsNotConnectedAndSubmitFormWithBadUsername()
    {
        // GIVEN
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'fakeuser',
            '_password' => 'fakepassword'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Nom d\'utilisateur invalide');
    }

    public function testLoginWhenUserIsNotConnectedAndSubmitFormWithBadPassword()
    {
        // GIVEN
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'User 0',
            '_password' => 'fakepassword'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
        $this->assertSelectorTextContains('.alert.alert-danger', 'Mot de passe invalide');
    }

    public function testLoginWhenUserIsNotConnectedAndSubmitFormWithGoodCredentials()
    {
        // GIVEN
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'User 0',
            '_password' => '123'
        ]);

        // WHEN
        $this->client->submit($form);

        // THEN
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h2', 'Salut, User 0');
    }
}