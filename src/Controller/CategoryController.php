<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category; //category vide pour l'instant

        $form = $this->createForm(CategoryType::class, $category); //création du formulaire a partir de CategoryType

        $form->handleRequest($request); //requeste analyse et traite la request http


        //sumission du formulaire
        if ($form->isSubmitted()) {
            $category->setSlug(strtolower($slugger->slug($category->getName()))); //j'ai fais la proprieté slug manuellement

            $em->persist($category); //sauvegarder ma category créer
            $em->flush(); //mettre la category créer en base de données

            return $this->redirectToRoute('homepage');
        }

        $form = $form->createView(); //affichage du formulaire avec twig

        return $this->render('category/create.html.twig', [
            'form' => $form
        ]);
    }


    #[Route('/admin/category/{id}/edit', name: 'category_edit')]
    public function edit($id, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = $categoryRepository->find($id); //category rempli 
        $form = $this->createForm(CategoryType::class, $category); // la category ciblé par son id

        $form->handleRequest($request);

        if($form->isSubmitted())
        {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $form = $form->createView();

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form
        ]);
    }
}