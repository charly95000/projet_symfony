<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Category;
use App\Form\CategoryType;

#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'category')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/add' , name : 'add_category')]
    public function add(Request $request, ManagerRegistry $doctrine): Response
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('category');
        }
        return $this->render('category/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/update/{id}', name: "update_category")]
    public function update(Request $request, ManagerRegistry $doctrine, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager = $doctrine->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
            return $this->redirectToRoute('category');
        }
        return $this->render('category/form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}', name: "delete_category")]
    public function delete(Request $request, Category $category, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($category);
        $entityManager->flush();
        return $this->redirectToRoute('category');
    }
}
