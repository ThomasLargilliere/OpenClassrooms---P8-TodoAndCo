<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{

    public function __construct(private UserRepository $repository, private UserPasswordHasherInterface $passwordHasher){}

    public function getUser(): Array
    {
        return $this->repository->findAll();
    }

    public function createUser(User $user): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            )
        );

        $this->repository->add($user, true);
    }

    public function editUser(User $userEditer, User $userEdited): bool
    {

        if (!$this->isAdminUser($userEditer)){
            return false;
        }

        $userEdited->setPassword(
            $this->passwordHasher->hashPassword(
                $userEdited,
                $userEdited->getPassword()
            )
        );

        $this->repository->update();
        return true;
    }

    public function isAdminUser(User $user): bool
    {
        if ($user->getRoles()[0] === 'ROLE_ADMIN'){
            return true;
        }
        return false;
    }
}