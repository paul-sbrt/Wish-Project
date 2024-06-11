<?php

namespace App\Controller;


use App\Entity\Category;
use App\Entity\User;
use App\Entity\Wish;
use App\Form\CategoryType;
use App\Form\UserEditFormType;
use App\Form\UserInfoEditFormType;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

#[Route('/profile') ]

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(WishRepository $repo): Response
    {


        $wishes = $repo->findBy(['isPublished' => "true"], ['dateCreated' => "DESC"]);
        return $this->render('main/index.html.twig', [
            'wishes' => $wishes

        ]);
    }

    #[Route('/detail/{id}', name: 'main_detail')]
    public function detail(Wish $wish): Response
    {

        return $this->render('main/detail.html.twig', [
            'wish' => $wish

        ]);
    }


    #[Route('/add', name: 'main_add')]
    public function add(EntityManagerInterface $em, Request $request)
    {

        $wish = new Wish(); // crée l'Entity wish
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        // Si l'utilisateur est connecté, assigne l'utilisateur comme auteur du souhait
        $wish->setAuthor($user -> getUsername());
        //je relie formulaire  $wishForm (WishType)<=> Wish Entity
        $wishForm = $this->createForm(WishType::class, $wish);
        // J'hydrate avec le formulaire    $WishForm (WishType)<=> Request
        $wishForm->handleRequest($request);



        // si le form SUBMIT
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            #$wish->setPublished(true);

            #$wish->setDateCreated(now());
            $em->persist($wish);
            $em->flush();
            $this->addFlash('success', 'Your Wish has been added successfully!');
            return $this->redirectToRoute("app_main");
        }
        return $this->render('main/add.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'main_edit')]
    public function edit(Wish $wish, EntityManagerInterface $em, Request $request): Response
    {

        $wishForm = $this->createForm(WishType::class, $wish);


        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Your Wish has been edited successfully!');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('main/edit.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }


    #[Route('/delete/{id}', name: 'main_delete')]
    public function delete(Wish $wish, EntityManagerInterface $em, Request $request): Response
    {

        $em->remove($wish);
        $em->flush();

        // Redirection après suppression
        $this->addFlash('danger', 'Your Wish has been delete successfully!');
        return $this->redirectToRoute('app_main');


    }
    #[Route('/user/info/{id}', name: 'user_info')]
    public function Info(User $user): Response
    {
        return $this->render('user/info.html.twig', [
            'user' =>$user,
        ]);
    }

    #[Route('/user/edit/{id}', name: 'edit_user_normal')]
    public function edituser (User $user, EntityManagerInterface $em, Request $request): Response
    {

        $userForm = $this->createForm(UserInfoEditFormType::class, $user);


        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $roles = $userForm->get('roles')->getData();
            $user->setRoles($roles);
            $em->flush();
            $this->addFlash('success', 'client had been edited successfully!');
            return $this->redirectToRoute('main_user');
        }

        return $this->render('user/editInfo.html.twig', [
            'userForm' => $userForm->createView(),
        ]);
    }

}






