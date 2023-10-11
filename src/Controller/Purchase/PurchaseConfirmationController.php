<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController extends AbstractController
{
    protected $cartService;
    protected $em;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, Security $security, CartService $cartService, EntityManagerInterface $em)
    { 
        $this->cartService = $cartService;
        $this->em = $em;
    }

    #[Route('/purchase/confirm', name:"purchase_confirm")]
    #[IsGranted("ROLE_USER", message:"Vous devez être connecter pour confirmer une commande")]
    public function confirm(Request $request, FlashBagInterface $flashBag) 
    //* on ne peut pas demander la request dans le __construt, car elle est lié à la route, donc elle est unique (changante)
    {
        // 1. Nous voulons lire les données du formulaire
        // FormFactoryInterface/Request
        $form = $this->createForm(CartConfirmationType::class);

        $form->handleRequest($request); //? analyse la Request

        // 2. Si le formulaire n'a pas été soumis : dégager
        if(!$form->isSubmitted()){
            // message Flash puis redirection (FlashBagIinterface)
            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');

            return $this->redirectToRoute('cart_show');
        }
        
        // 3. Si je ne suis pas connecté : dégager (Security)
        // * #[IsGranted("ROLE_USER")]
        $user = $this->getUser();

        // 4. Si il y a pas de produits dans le panier : dégager (CartService où SessionInterface)
        $cartItems = $this->cartService->getDetailedcartItems();

        if(count($cartItems) === 0){ //? si il y a pas dans cartItems de item (si le compte === 0) panier vide
            $this->addFlash('warning', 'Vous ne pouvez pas confirmer la commande avec un panier vide');

            return $this->redirectToRoute("cart_show");   
        }

        // 5. Nous allons céer une purchase
        /** @var Purchase */
        $purchase = $form->getData(); 
        //* c'est un tableau assotiatif à la base, mais quand on ajoute l'option 'data_class' => Purchase::class dans la fonction d'option dans le CartConfirmationType il deviens une class de l'entity Purchase
      
        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime())
            ->setTotal($this->cartService->getTotal())
            ;
            

        $this->em->persist($purchase);

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService) 
        $totalPurchaseAmount = 0;  
        foreach($this->cartService->getDetailedcartItems() as $cartItem){
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice())
            ;

            $this->em->persist($purchaseItem);

            $totalPurchaseAmount += $purchaseItem->getTotal();
        }
        $purchase->setTotal($totalPurchaseAmount);

        // 8. Nous allons enregister la commande (EntitymanagerInterface)
        $this->em->flush();

        $this->cartService->empty();

        $this->addFlash('success', "La commande a bien été enregistrer");

        return $this->redirectToRoute('purchase_index');
    }
}