<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

// #[IsGranted("ROLE_ADMIN", message:"Vous n'avez le droit")] //? interdit l'action a toutes les methode sauf a l'admin
class CategoryController extends AbstractController
{
    // protected $categoryRepository;

    // public function __construct(CategoryRepository $categoryRepository)
    // {
    //     $this->categoryRepository = $categoryRepository;
    // }

    // public function renderMenuList()
    // {
    //     $categories = $this->categoryRepository->findAll();

    //     return $this->render('category/_menu.html.twig', [
    //         'categories' => $categories
    //     ]);
    // }

    #[Route('/admin/category/create', name: 'category_create')]
    public function create(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $category = new Category; //category vide pour l'instant

        $form = $this->createForm(CategoryType::class, $category); //création du formulaire a partir de CategoryType

        $form->handleRequest($request); //requeste analyse et traite la request http


        //sumission du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
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
        $category = $categoryRepository->find($id); //category recupéré par son id 

        if(!$category){
            throw new NotFoundHttpException("Cette catégorie n'existe pas");
        }

        $form = $this->createForm(CategoryType::class, $category); // la category ciblé par son id

        $form->handleRequest($request); //analyse la requête http

        if($form->isSubmitted() && $form->isValid()) //submission du formulaire
        {
            $category->setSlug(strtolower($slugger->slug($category->getName())));

            $em->flush();// envoyer en base de donnéer

            return $this->redirectToRoute('homepage'); //a la soumission du formulaire la redirection vers la page qu'on veux par son name:''
            
        }

        $form = $form->createView();//creation de l'affichage avec twig

        return $this->render('category/edit.html.twig', [
            'category' => $category, //parametre pour afficher la category 
            'form' => $form //parametre mpour afficher la category
        ]);
    }
}
