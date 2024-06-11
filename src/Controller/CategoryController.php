<?php

namespace App\Controller;

use App\Entity\Category;

use App\Form\CategoryType;

use App\Repository\CategoryRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin') ]

class CategoryController extends AbstractController
{


    #[Route('/Category', name: 'app_category')]
    public function category(CategoryRepository $repo): Response
    {

        $category = $repo->findAll();
        return $this->render('category/category.html.twig', [
            'category' => $category

        ]);
    }

    #[Route('/addCat', name: 'main_addCat')]
    public function add(EntityManagerInterface $em, Request $request)
    {
        $categ = new Category(); // crée l'Entity wish
        //je relie formulaire  $wishForm (WishType)<=> Wish Entity
        $cateForm = $this->createForm(CategoryType::class, $categ);
        // J'hydrate avec le formulaire    $WishForm (WishType)<=> Request
        $cateForm->handleRequest($request);
        // si le form SUBMIT
        if ($cateForm->isSubmitted() && $cateForm->isValid()) {
            $em->persist($categ);
            $em->flush();
            $this->addFlash('success', 'Your category has been added successfully!');
            return $this->redirectToRoute("app_category");
        }
        return $this->render('category/addCat.html.twig', [
            'cateForm' => $cateForm->createView(),
        ]);

    }

    #[Route('/category/delete/{id}', name: 'category_delete')]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {

        $em->remove($category);
        $em ->flush();


        // Redirection après suppression
        $this->addFlash('danger', 'Your Wish has been delete successfully!');
        return $this->redirectToRoute('app_category');


    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function edit(Category $categ, EntityManagerInterface $em, Request $request): Response
    {

        $categoryForm = $this->createForm(CategoryType::class, $categ);


        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Your category has been edited successfully!');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/editCat.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }

}