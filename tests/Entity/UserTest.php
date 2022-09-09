<?php

namespace App\Tests\Service;

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
}