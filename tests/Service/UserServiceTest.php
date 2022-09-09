<?php

namespace App\Tests\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserServiceTest extends KernelTestCase
{
    private UserService $userService;

    protected function setUp(): void
    {
        $this->userService = static::getContainer()->get(UserService::class);
    }

    public function testGetUser()
    {
        // WHEN
        $result = $this->userService->getUser();

        // THEN
        $this->assertClassHasAttribute('id', User::class);
        $this->assertClassHasAttribute('username', User::class);
        $this->assertClassHasAttribute('email', User::class);
        $this->assertClassHasAttribute('password', User::class);
        $this->assertClassHasAttribute('roles', User::class);
    }

    public function testCreateUser()
    {
        // GIVEN
        $user = (new User)->setPassword('123')->setUsername("Test")->setEmail("test@test.fr");

        // WHEN
        $result = $this->userService->createUser($user);

        // THEN
        $this->assertEmpty($result);
    }

    public function testIsAdminWhenUserConnectedIsAdmin()
    {
        // GIVEN
        $user = (new User)->setRoles(['ROLE_ADMIN']);

        // WHEN
        $result = $this->userService->isAdminUser($user);

        // THEN
        $this->assertTrue($result);
    }

    public function testIsAdminWhenUserConnectedIsNotAdmin()
    {
        // GIVEN
        $user = new User();

        // WHEN
        $result = $this->userService->isAdminUser($user);

        // THEN
        $this->assertFalse($result);
    }
}