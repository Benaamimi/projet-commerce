<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
    public $product;
    public $qty;

    public function __construct(Product $product, int $qty)
    {
        $this->product = $product;
        $this->qty = $qty;
    }

    //? le total de tous ce qu'il y a dans le panier
    public function getTotal(): int
    {
        //* on peut ajouter ici nos régle de gestion par exemple : promo, reduction etc...

        return $this->product->getPrice() * $this->qty;
    }


}

