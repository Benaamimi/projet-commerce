<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartController extends AbstractController
{

    
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements:["id" => "\d+"])] //? accepte que des valeur numeric
    //? Les ArgumentResolver analyse la fonction : parametre de routes, la request elle même, Les services du Container 
    public function add($id, Request $request): Response
    {
        // pour le panier:
        // 0. Securisation : est-ce que le produit existe ?
        $product = $this->productRepository->find($id);
        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        //? fonction add de la class CartService, que j'ai créer dans src/Cart/CartSevice pour le gérer le panier
        $this->cartService->add($id); 

        //? appeller directement de avec la methode addFlash dans l'AbstractConttroller
        $this->addFlash('success', "Le produit a bien été ajouté au panier"); 

        //? apeller avec la fonction add de la class (FlashBagInterface $flashBag)
        // $flashBag->add('success', "Le produit a bien été ajouté au panier"); 
        //? le 'success' est le nom de la class boostrap dans twig voir : base.html.twig

        if($request->query->get('returnToCart')){ 
        //? si dans la query il y a ?returnToCart, ce que j'ai mis dans twig, alors il faut ce rediriger ver la route cart_show 
            return $this->redirectToRoute("cart_show");
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    #[Route('/cart', name:"cart_show")]
    public function show(CartService $cartService): Response
    {
         //? fonction getDetailedcartItems de la class CartService
       $detailedCart = $cartService->getDetailedcartItems();

         //? fonction getTotal de la class CartService
       $total = $cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }

    #[Route('/cart/delete/{id}', name:"cart_delete", requirements:['id' => '\d+'])]
    public function delete($id): Response
    {

        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas est ne peut pas être supprimé !");
        }

        $this->cartService->remove($id);

        $this->addFlash("success", "Le produit a bien été supprimé du panier");

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/decrement/{id}', name:"cart_decrement", requirements:["id" => "\d+"])]
    public function decrement($id): Response
    {
        $product = $this->productRepository->find($id);

        if(!$product){
            throw $this->createNotFoundException("Le produit $id n'existe pas est ne peut pas être retirer !");
        }

        $this->cartService->decrement($id);

        $this->addFlash("success", "Le produit a bien été retirer");

        return $this->redirectToRoute('cart_show');
    }


}
