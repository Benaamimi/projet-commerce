<?php

namespace App\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $cartService;
    protected $em;
    protected $security;

    public function __construct( CartService $cartService, EntityManagerInterface $em, Security $security)
    { 
        $this->cartService = $cartService;
        $this->em = $em;
        $this->security = $security;
    }

    public function storePurchase(Purchase $purchase)
    {
        //Intégrer tout ce qu'il faut et persister la purchase

        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal());


        $this->em->persist($purchase);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService) 
        $totalPurchaseAmount = 0;
        foreach ($this->cartService->getDetailedcartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice());

            $this->em->persist($purchaseItem);

            $totalPurchaseAmount += $purchaseItem->getTotal();
        }
        $purchase->setTotal($totalPurchaseAmount);

        // 8. Nous allons enregister la commande (EntitymanagerInterface)
        $this->em->flush();
    }
}
