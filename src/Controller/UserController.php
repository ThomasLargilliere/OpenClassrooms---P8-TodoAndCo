<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\UserService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{

    public function __construct(private UserService $userService){}

    #[Route('/users', name: 'user_list')]
    public function listUser()
    {
        $user = $this->getUser();

        $userIsConnected = $this->userService->userIsConnected($user);
        if (!$userIsConnected){
            return $this->needLogin();
        }

        $isAdminUser = $this->userService->isAdminUser($user);
        if (!$isAdminUser){
            return $this->needAdmin();            
        }

        $users = $this->userService->getUser();
        return $this->render('user/list.html.twig', ['users' => $users]);
    }


    #[Route('/users/create', name: 'user_create')]
    public function createUser(Request $request)
    {
        $user = new User();
        $form = $this->createForm(\App\Form\UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->createUser($user);
            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_create');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/users/{id}/edit', name: 'user_edit')]
    public function editUser(Request $request, User $user)
    {
        $userEditer = $this->getUser();

        $userIsConnected = $this->userService->userIsConnected($userEditer);
        if (!$userIsConnected){
            return $this->needLogin();
        }

        $isAdminUser = $this->userService->isAdminUser($userEditer);
        if (!$isAdminUser){
            return $this->needAdmin();            
        }

        $form = $this->createForm(\App\Form\UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $result = $this->userService->editUser($userEditer, $user);

            $typeFlash = 'success';
            $messageFlash = 'L\'utilisateur a bien été modifié.';

            if (!$result){
                $typeFlash = 'error';
                $messageFlash = 'L\'utilisateur n\'a pas pu être modifié, vous devez être administrateur pour faire cela.';
            }

            $this->addFlash($typeFlash, $messageFlash);
            return $this->redirectToRoute('user_create');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

    public function needLogin()
    {
        $this->addFlash('error', 'Vous devez être connecté pour voir la liste des utilisateurs');
        return $this->redirectToRoute('app_login');          
    }
    public function needAdmin()
    {
        $this->addFlash('error', 'Vous devez être administrateur pour voir la liste des utilisateurs');
        return $this->redirectToRoute('app_login');        
    }
}
