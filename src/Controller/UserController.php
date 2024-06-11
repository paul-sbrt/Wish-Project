<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\AdduserFormType;
use App\Form\RegistrationFormType;
use App\Form\UserEditFormType;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/admin') ]
class UserController extends AbstractController
{
    #[Route('/user', name: 'main_user')]
    public function user(UserRepository $repo): Response
    {
        $user = $repo ->findAll();

        return $this->render('user/list.html.twig', [
            'user' =>$user,
        ]);
    }

    #[Route('/user/delete/{id}', name: 'delete_user')]
    public function delete(User $user,EntityManagerInterface $em, Request $request): Response
    {
        $em->remove($user);
        $em->flush();

        // Redirection après suppression
        $this->addFlash('danger', ' User has been delete successfully!');
        return $this->redirectToRoute('main_user');

    }
    #[Route('/user/edit/{id}', name: 'edit_user')]
    public function edit(User $user, EntityManagerInterface $em, Request $request): Response
    {

        $userForm = $this->createForm(UserEditFormType::class, $user);


        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $roles = $userForm->get('roles')->getData();
            $user->setRoles($roles);
            $em->flush();
            $this->addFlash('success', 'client had been edited successfully!');
            return $this->redirectToRoute('main_user');
        }

        return $this->render('user/edit.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }
    #[Route('/add', name: 'add_user')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(AdduserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $this->addFlash('success', 'client had been edited successfully!');
            return $this->redirectToRoute('main_user');
        }

        return $this->render('user/add.html.twig', [
            'addForm' => $form,
        ]);
    }



}


