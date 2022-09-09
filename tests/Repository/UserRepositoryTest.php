<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = static::getContainer()->get(UserRepository::class);
    }

    public function testRemoveUser()
    {
        // GIVEN
        $user = (new User)->setUsername('toto')->setPassword('123')->setEmail('toto@toto.fr');
        $this->userRepository->add($user, true);
        $userToDelete = $this->userRepository->findOneByUsername('toto'); 

        // WHEN
        $result = $this->userRepository->remove($userToDelete);

        // THEN
        $this->assertEmpty($result);
    }
}