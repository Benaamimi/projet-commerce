<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category($slug, CategoryRepository $categoryRepo): Response
    {
        $category = $categoryRepo->findOneBy([
            'slug' => $slug
        ]);


        if (!$category) {
            //    throw new NotFoundHttpException("La catégorie demandé n'existe pas !"); //affichage de l'erreur 404
            throw $this->createNotFoundException("404 NOT FOUND CATEGORY"); //method erité de AbstractController
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    #[Route("/{category_slug}/{slug}", name: "product_show", priority: -1)]
    public function show($slug, ProductRepository $productRepo, EventDispatcherInterface $dispatcher): Response
    {
        $product = $productRepo->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("404 NOT FOUND PRODUCT");
        }

        $productEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productEvent, 'product.view');

        

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }


    #[Route("/admin/product/create", name: 'product_create')]
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {


        $product = new Product; //? equivalent a $product = $form->getData()

        $form = $this->createForm(ProductType::class, $product); //? création du formulaire a partir de la class product avec le productType::class

        $form->handleRequest($request); //! inspercte la requette si le formulaire a été soumis il prend les infos mis dans le formulaire

        if ($form->isSubmitted() && $form->isValid()) {
            //* $product = $form->getData(); //si les le formulaire a été soumis il prend les infos du formlulaire
            $product->setSlug(strtolower($slugger->slug($product->getName()))); //* mettre le slug du nom du produit grace a $slugger

            $em->persist($product); //? perparer
            $em->flush(); //? mettre en base de données

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }


        $formView = $form->createView(); //! la fonction createView pour l'afficher avec twig

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }

    #[Route('/admin/product/{id}/edit', name: 'product_edit')]
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em, SluggerInterface $slugger, ValidatorInterface $validator) //la request pour le submit du formulaire et $em pour les modif dans la bdd
    {

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product); //* $product dans la fonction et fait la même chose que $form->setData($product);
        // $form->setData($product); //les information du produit cibler dans la route par sont id /admin/product/{id}/edit grace à la methode find($id)

        $form->handleRequest($request); //* inspercte la requette si le formulaire a été soumis il prend les infos mis dans le formulaire poue les placer dans product

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName()))); //* mettre le slug du nom du produit grace a $slugger
            $em->flush(); //? pas besoin de persiste pcq le produit exist déjà, ce flush confirme les modifications et les envoie en base de données

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug(),
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
            'product' => $product, //? le product cibler par son id
            'formView' => $formView //? variable 'formView' on lui passe =>  $formView qui contient = $form->createView() pour l'afficher avec twig
        ]);
    }
}
