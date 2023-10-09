<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * * Class qui gére toutes les fonctionalité du panier
 */
class CartService 
{
    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []); //? sortir le panier
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart); //? mettre a jour la session 
    }


    public function add(int $id, )
    {
         // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. Si il n'existe pas encore, alors prendre le tableau vide
        $cart = $this->getCart();

        // [12 => 3, 29 => 2]
        // 3. Voir si le produit ($id) existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon, ajouter le produit avec la quantié 1
            if(!array_key_exists($id, $cart)){
                $cart[$id] = 0;
            }
            $cart[$id]++;
            

        // 6. Enregistrer le tableau mis à jour dans la session
            $this->saveCart($cart);
    }

    //? fonction pour supprimer un article du panier
    public function remove(int $id)
    {
        $cart = $this->getCart(); //? sortir le panier

        unset($cart[$id]); //? supprimer la donnée qui se trouve dans le tableau cart par sont id

        $this->saveCart($cart); //? mettre a jour la session 
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart(); //? sortir le panier

        if(!array_key_exists($id, $cart)){ //? si il n'existe pas une ligne avec l'indentifiant ciblé dans le pas je fait rien 
            return;
        }
        //? si le produit est 1 dans le panier il faut supprimer 
        if($cart[$id] === 1){
            $this->remove($id);
            return;
        }
        //? si le produit est à plus de 1 il faut le décrémenté
        $cart[$id]--;
        $this->saveCart($cart); //? mettre à le panier avec le produit décrémenté 

    }

    public function getTotal(): int
    {
        $total = 0;

        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);

            if(!$product){ //? si pas de produit la bloucle continue
                continue;
            }

            $total += $product->getPrice() * $qty;  
        }

        return $total;
    }
    
    public function getDetailedcartItems(): array
    {
        $detailedCart = [];

        //? les produits du panier
        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);

            if(!$product){
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty); //? de la class CartItem dans src/Cart/CartItem
        }

        return $detailedCart;
    }



}